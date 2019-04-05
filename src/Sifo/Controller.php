<?php

namespace Sifo;

use Exception;

abstract class Controller
{
    /** @var int */
    const CACHE_DEFAULT_EXPIRATION_EXCEPTIONS = 10; // secs.
    /** @var int */
    const CACHE_DEFAULT_EXPIRATION = 14400; // secs.
    private const BENCHMARK_PARENT_KEY = 'controller_execution_time_parent';
    private const BENCHMARK_REPLACEMENT_KEY = 'controller_execution_replace';
    private const BENCHMARK_CURRENT_KEY = 'controller_execution_time';
    /** @var array */
    protected $debug_info = [];
    /** @var array */
    protected $modules = [];
    /** @var bool */
    public $is_json = false;
    /** @var array */
    protected $params;
    /** @var string */
    protected $language;
    /** @var View */
    protected $view;
    /** @var Cache */
    protected $cache;
    /**
     * Stores the cache definition once is calculated for the next queries.
     *
     * @var string
     */
    protected $cache_definition;
    /**
     * The dependency injection container.
     *
     * @var DependencyInjector|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /** @var I18N */
    public $i18n;
    /** @var string */
    protected $layout;
    /** @var string */
    private $instance;
    /** @var array */
    private $url_definition;

    /**
     * @throws Exception_404
     * @throws Exception_500
     */
    public function __construct()
    {
        $this->instance = Bootstrap::$instance;
        $this->language = Domains::getInstance()->getLanguage();
        $this->url_definition = Urls::getInstance($this->instance)->getUrlDefinition();

        $urls = Urls::getInstance($this->instance)->getUrlConfig();
        $current_url = $this->getUrl(Urls::getInstance(Bootstrap::$instance)->getPath(), Urls::getInstance($this->instance)->getParams());

        $urls['current_url'] = $current_url;
        $this->params = [
            'current_url' => $current_url,
            'instance' => Bootstrap::$instance,
            'controller' => get_class($this),
            'path' => Urls::getInstance($this->instance)->getPath(),
            'path_parts' => Urls::getInstance($this->instance)->getPathParts(),
            'params' => Urls::getInstance($this->instance)->getParams(),
            'has_debug' => Domains::getInstance()->getDebugMode(),
            'lang' => $this->language,
            'url' => $urls,
        ];

        $this->i18n = I18N::getInstance(Domains::getInstance()->getLanguageDomain(), $this->language);

        $this->params['parsed_params'] = $this->parseParams();
        $this->params['page'] = $this->getCurrentPage();

        $this->cache = Cache::getInstance(Cache::CACHE_TYPE_AUTODISCOVER);
        $this->view = new View();
    }

    /** @throws SEO_Exception */
    abstract function build();

    /**
     * @throws ControllerException
     * @throws Exception_500
     * @throws Exception_DependencyInjector
     */
    public function dispatch()
    {
        $this->checkKillSession();
        $this->addJsonHeadersIfRequired();

        $this->startBench(self::BENCHMARK_PARENT_KEY);

        $this->preDispatch();

        $cached_content = $this->grabCache();
        if (false !== $cached_content) {
            $this->outputCachedContent($cached_content);
            return;
        }

        $complete_cache_definition = $this->getCompleteCacheDefinition();

        if (false !== $complete_cache_definition) {
            $this->addToDebug('name', $complete_cache_definition['name'], 'Cache properties');
            $this->addToDebug('expiration', $complete_cache_definition['expiration'], 'Cache properties');
        }

        try {
            $return_values = $this->build();
        } catch (SEO_Exception $e) {
            $this->cacheException($e, $complete_cache_definition);
            throw new ControllerException("Controller Build has generated an exception.", null, $e);
        }

        $controller_params = array_merge(['layout' => $this->layout], $this->getParams());
        $this->addToDebug('parameters', $controller_params, 'CONTROLLER');
        $this->executeNestedModules();

        if ($this->is_json) {
            $content = $this->grabJson($return_values);
        } else {
            $content = $this->grabHtml();
        }

        if (false !== $complete_cache_definition) {
            $this->cache->set($complete_cache_definition['name'], $content, $complete_cache_definition['expiration']);
        }

        $this->postDispatch();
        $this->stopBench(self::BENCHMARK_PARENT_KEY, "----- TOTAL " . get_class($this) . " + PREVIOUS MODULES -----");

        $content = $this->realTimeReplacement($content);
        Headers::send();

        if (extension_loaded('newrelic')) {
            newrelic_name_transaction($this->params['controller']);
        }

        $this->outputContent($content);
    }

    /**
     * Dispatch a single controller. Fetch from cache (if any), execute, store cache, return output.
     *
     * @param string $module_name Name of controller to execute.
     * @param array $params Additional parameters needed by the controller
     * @return string
     * @throws Exception_DependencyInjector
     * @throws ControllerException
     */
    public function getModuleOutput(
        $module_name,
        $params = []
    ) {
        $this->startBench(self::BENCHMARK_CURRENT_KEY);

        $module = Bootstrap::invokeController($module_name);
        $module->setParams(array_merge($this->getParams(), $params));
        $module->preDispatch();
        $cached_content = $module->grabCache();
        $class_name = get_class($module);

        if (false !== $cached_content) {
            if ($cached_content instanceof Exception) {
                throw new ControllerException("Module Execute has generated an exception (cached).", null, $cached_content);
            }
            $module_content = $cached_content;
        } else {
            $cache_key = $module->getCompleteCacheDefinition();
            if (false !== $cache_key) {
                $module->addToDebug('name', $cache_key['name'], 'Cache properties');
                $module->addToDebug('expiration', $cache_key['expiration'], 'Cache properties');
            }
            try {
                $module_content = $module->getOutput();
            } catch (SEO_Exception $e) {
                $this->cacheException($e, $cache_key);
                throw new ControllerException("Module Execute has generated an exception.", null, $e);
            }
        }

        $cache_key = $module->getCompleteCacheDefinition();

        if (false !== $cache_key) {
            $this->cache->set($cache_key['name'], $module_content, $cache_key['expiration']);
        }

        $module->postDispatch();
        $this->stopBench(self::BENCHMARK_CURRENT_KEY, "$class_name: TOTAL module execution");

        return $module_content;
    }

    /**
     * Stops the execution of the current controller in order to dispatch the given controller.
     *
     * @param string $controller Controller in the format 'folder/file'
     */
    public function reDispatch($controller)
    {
        Bootstrap::dispatch($controller);
        exit;
    }

    /**
     * Actions executed BEFORE the controller executes build() or cache is called.
     */
    public function preDispatch()
    {
    }

    /**
     * Actions executed AFTER the controller has executed build() and cache fetched and right before the output is sent to browser.
     *
     */
    public function postDispatch()
    {
    }

    public function setContainer(DependencyInjector $container)
    {
        $this->container = $container;
    }

    public function getAssignedVars()
    {
        return $this->view->getTemplateVars();
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function getUrl(
        $relative_path,
        $params = null
    ) {
        $url = Urls::getInstance($this->instance)->getUrl($relative_path);
        if ((!$url) && ('' != $relative_path)) {
            /*
             * The relative_path exists but the first getUrl try not found it.
             * We try with the path translations:
            */
            if (Router::getReversalRoute($relative_path)) {
                if (!($url = Urls::getInstance($this->instance)->getUrl(Router::getReversalRoute($relative_path)))) {
                    // Fixed the current_url in url like word1_word2 for url word1-word2
                    $url = Urls::getInstance($this->instance)->getUrl(Router::getReversalRoute(str_replace('-', '_', $relative_path)));
                }
            } else {
                $url = Urls::$base_url . '/' . $relative_path;
            }
        }

        if ($params) {
            $url .= $this->url_definition['params_separator'];

            if (is_array($params)) {
                $url .= implode($this->url_definition['params_separator'], $params);
            } else {
                $url .= $params;
            }
        }

        return $url;
    }

    /**
     * Sets a template (relative path) as the template that triggers the page.
     *
     * @param string $template
     * @throws Exception_Configuration
     */
    public function setLayout($template)
    {
        $this->layout = $this->getTemplate($template);
    }

    /**
     * Returns the absolute path to a template. Customized templates are specified in the configuration files.
     *
     * @param string $template
     * @return string
     * @throws Exception_Configuration
     */
    public function getTemplate($template)
    {
        return ROOT_PATH . '/' . Config::getInstance($this->instance)->getConfig('templates', $template);
    }

    public function assign(
        $tpl_var,
        $value
    ) {
        if ($tpl_var != 'modules') {
            $this->addToDebug($tpl_var, $value, 'assigns');
        }
        $this->view->assign($tpl_var, $value);
    }

    /**
     * @throws ControllerException
     * @throws Exception_DependencyInjector
     */
    protected function executeNestedModules()
    {
        if (empty($this->modules)) {
            return;
        }

        $modules = [];
        foreach ($this->modules as $module_name => $controller) {
            $modules[$module_name] = $this->getModuleOutput($controller, ['module_name' => $module_name]);
        }
        unset($this->modules);

        $this->assign('modules', $modules);
    }

    /**
     * Cache a resulting exception when a cache_key is defined and hasn't any Post vars.
     * Use the CACHE_DEFAULT_EXPIRATION_EXCEPTIONS for all the exception less 301,302 and 404.
     *
     * @param SEO_Exception $exception Cached exception.
     * @param bool|array $cache_key
     */
    protected function cacheException(
        $exception,
        $cache_key
    ) {
        if (empty($cache_key) || FilterPost::getInstance()->countVars() > 0) {
            return;
        }
        $expiration = in_array($exception->http_code, [301, 302, 404]) ? $cache_key['expiration'] : self::CACHE_DEFAULT_EXPIRATION_EXCEPTIONS;
        $this->cache->set($cache_key['name'], $exception, $expiration);
    }

    /**
     * Grabs the HTML for a smarty template.
     *
     * @return string
     * @throws Exception_500
     */
    protected function grabHtml()
    {
        $class_name = get_class($this);
        if (!$this->is_json && !isset($this->layout)) {
            throw new Exception_500('Layout not set in controller ' . $class_name);
        }

        $this->startBench("view_$class_name");
        // Assign common vars:
        $this->assignCommonVars();

        // Add another key inside the debug key:
        Debug::subSet('controllers', $class_name, $this->debug_info);

        $content = $this->view->fetch($this->layout);
        $this->stopBench("view_$class_name", "$class_name: Smarty fetch");
        return $content;
    }

    /**
     * Returns tha contents in cache or false.
     *
     * @return string|bool
     */
    protected function grabCache()
    {
        // When DATA is sent, invalidate cache:
        if (0 < FilterPost::getInstance()->countVars()) {
            return false;
        }

        $cache_key = $this->getCompleteCacheDefinition();
        // Controller does not uses cache:
        if (!$cache_key) {
            return false;
        }

        Benchmark::getInstance()->timingStart('cache');
        $content = $this->cache->get($cache_key['name']);
        Benchmark::getInstance()->timingCurrentToRegistry('cache');

        if ($content) {
            if (false !== strpos(Cache::$cache_type, 'MEMCACHE')) {
                $this->addToDebug('Stored in Memcache as', sha1($cache_key['name']), 'Cache properties');
            }
            // Add another key inside the debug key:
            $this->addToDebug('Cache definition', $cache_key, 'Cache properties');

            Debug::subSet('controllers', get_class($this) . ' <small>- ' . Cache::$cache_type . ' HIT</small>', $this->debug_info);
            return $content;
        }

        return false; // Life was beautiful, but although everything seemed to be cached, it wasn't.
    }

    private function getCompleteCacheDefinition()
    {
        if (isset($this->cache_definition)) {
            return $this->cache_definition;
        }

        $this->cache_definition = $this->getCacheDefinition();

        if (false === $this->cache_definition || '' === $this->cache_definition) {
            return $this->cache_definition = false;
        }

        if (!is_array($this->cache_definition)) {
            $this->cache_definition = ['name' => $this->cache_definition];
        }

        if (empty($this->cache_definition['expiration'])) {
            $this->cache_definition['expiration'] = self::CACHE_DEFAULT_EXPIRATION;
        }

        // Prepend necessary values to cache:
        $this->cache_definition['name'] = $this->getFinalCacheKeyName($this->cache_definition);

        return $this->cache_definition;
    }

    /**
     * Returns the final cache name, prepending the necessary attributes.
     *
     * @param array $definition Cache definition.
     * @return string
     */
    private function getFinalCacheKeyName(array $definition)
    {
        // Add the controller class name when 'name' is empty.
        if (!isset($definition['name'])) {
            $definition['name'] = get_class($this);
        }

        return $this->cache->getCacheKeyName($definition);
    }

    /**
     * Returns the cache definition of this controller.
     *
     * A string with the cache key can be returned or an array with 'name' and 'expiration' (both mandatory).
     *
     * @return mixed
     */
    public function getCacheDefinition()
    {
        return false;
    }

    /**
     * Deletes the cache of a controller by name or array of properties.
     *
     * @param mixed $key_definition Array of keys=>values that define the cache,
     * or just a string that will be used as "name".
     * @return mixed
     */
    public function deleteCache($key_definition)
    {
        if (!is_array($key_definition)) {
            $key_definition = ['name' => $key_definition];
        }

        return $this->cache->delete($this->getFinalCacheKeyName($key_definition));
    }

    /**
     * Deletes all the cache keys that share a common tag at the specified value.
     *
     * @param string $tag
     * @param mixed $value
     * @return boolean
     */
    public function deleteCacheByTag(
        $tag,
        $value
    ) {
        return $this->cache->deleteCacheByTag($tag, $value);
    }

    /**
     * @throws Exception_Configuration
     */
    private function assignCommonVars()
    {
        $this->assign('url', $this->params['url']);
        $this->assign('_tpls', Config::getInstance()->getConfig('templates'));
    }

    /**
     * Returns the HTML of a smarty template or false if was impossible to fetch.
     *
     * @param string $template
     * @return boolean|string|null
     */
    public function fetch($template)
    {
        try {
            $template = $this->getTemplate($template);
            $this->assignCommonVars();
            return $this->view->fetch($template);
        } catch (Exception_Configuration $e) {
            return false;
        }
    }

    /**
     * Executes the current controller.
     *
     * @return string
     * @throws Exception_500
     * @throws SEO_Exception
     */
    private function getOutput()
    {
        $controller_params = array_merge(['layout' => $this->layout], $this->getParams());
        $this->addToDebug('parameters', $controller_params, 'CONTROLLER');

        $result = $this->build();

        if ($this->is_json) {
            return $result;
        }

        return $this->grabHtml();
    }

    /**
     * After the content is rendered all the tags <!-- REPLACE: are searched for module execution.
     *
     * This function allows to decrease the number of memcache sets.
     *
     * @param string $buffer HTML output.
     * @return string
     */
    private function realTimeReplacement($buffer)
    {
        $this->startBench(self::BENCHMARK_REPLACEMENT_KEY);

        // Only letters, numbers _ and . ALLOWED. Take care with parameters.
        $buffer = preg_replace_callback('/<\!--\s*REPLACE\:([a-zA-Z0-9:_\\\.\-,\/\+]*)\s*-->/', [$this, 'executeReplacementModule'], $buffer);

        $this->stopBench(self::BENCHMARK_REPLACEMENT_KEY, "---- TOTAL REALTIME REPLACEMENTS ----");
        return $buffer;
    }

    /**
     * Executes a module requested in the <!-- REPLACE --> tag and returns its output.
     *
     * @param array $matches Preg_replace matches.
     * @return string
     * @throws ControllerException
     * @throws Exception_DependencyInjector
     */
    private function executeReplacementModule($matches)
    {
        $replace_params = explode('::', $matches[1]);
        $controller = $replace_params[0];
        unset ($replace_params[0]);
        return $this->getModuleOutput($controller, ['params' => array_values($replace_params)]);
    }

    /**
     * @param $controller
     * @param array $params
     * @return string
     * @throws ControllerException
     * @throws Exception_DependencyInjector
     * @deprecated
     */
    public function dispatchSingleController(
        $controller,
        $params = []
    ) {
        return $this->getModuleOutput($controller, $params);
    }

    /**
     * Sends the output to the browser (echo), both cached or not.
     *
     * This is the last chance to modify the output.
     *
     * @param string $content
     */
    private function outputContent($content)
    {
        echo $content;
    }

    /**
     * Returns the parameters relative to this controller.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Returns a single specific parameter or false if not present.
     *
     * @param string $param_name
     * @return mixed
     */
    public function getParam($param_name)
    {
        if (isset($this->params[$param_name])) {
            return $this->params[$param_name];
        }

        return false;
    }

    /**
     * Return a param results from expected definition parse.
     *
     * @param string $param_name
     * @return mixed
     */
    public function getParsedParam($param_name)
    {
        if (!empty($this->params['parsed_params']) && isset($param_name, $this->params['parsed_params'], $this->params['parsed_params'][$param_name])) {
            return $this->params['parsed_params'][$param_name];
        }
        return false;
    }

    /**
     * Gets the given parameter number passed in the URL as "param", defined in the url_definition.
     *
     * @param integer $number
     * @return string
     */
    public function getUrlParam($number)
    {
        if (isset($this->params['params'][$number])) {
            return $this->params['params'][$number];
        }
        return false;
    }

    /**
     * Returns the translation of a string
     *
     * @param string $subject
     * @param string $var_1
     * @param string $var2
     * @param string $var_n
     * @return string
     */
    public function translate(
        $subject,
        $var_1 = '',
        $var2 = '',
        $var_n = ''
    ) {
        $args = func_get_args();

        $variables = [];
        if (1 < count($args)) {
            foreach ($args as $key => $value) {
                $variables['%' . $key] = $value;
            }
        }

        unset($variables['%0']);
        return $this->i18n->getTranslation($subject, $variables);
    }

    /**
     * Reset the parameters of the controller with the new ones passed.
     *
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Add new parameters to the params array on the controller.
     *
     * @param array $params
     */
    public function addParams(Array $params)
    {
        $this->params = array_merge($this->params, $params);
    }

    /**
     * Adds a module in the battery to be executed later.
     *
     * @param string $name
     * @param string $controller
     */
    public function addModule(
        $name,
        $controller
    ) {
        $this->modules[$name] = $controller;
    }

    /**
     * Add modules in the battery from an array.
     *
     * @param array $modules List of modules to load.
     */
    public function addModules(array $modules = [])
    {
        foreach ($modules as $key => $val) {
            $this->modules[$key] = $val;
        }
    }

    /**
     * Get a list of common modules merged with custom ones retreived from child controller.
     *
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Adds an element in the debug as a new entry. You can set the context to create groups.
     *
     * @param string $key
     * @param mixed $value
     * @param string $context
     */
    protected function addToDebug(
        $key,
        $value,
        $context = null
    ) {
        // Store everything in the debug in the registry.
        if (Domains::getInstance()->getDebugMode()) {
            if (null === $context) {
                $context = '__other__';
            }

            $this->debug_info[$context][$key] = $value;
        }
    }

    /**
     * Starts the timer for the "benchmark" in the debug.
     *
     * @param string $key A key to identify this timer. Used to stop it also.
     */
    protected function startBench($key)
    {
        Benchmark::getInstance()->timingStart($key);
    }

    /**
     * Stops the timer for the bench.
     * @param string $key Identifier that you used to start the bench.
     * @param string $label Text that will be shown in the benchmarks table.
     */
    protected function stopBench(
        $key,
        $label
    ) {
        Debug::subSet('benchmarks', $label, Benchmark::getInstance()->timingCurrent($key));
    }

    /**
     * Get config from the current instance or a given one.
     *
     * @param string $config_name Config name.
     * @param string $instance If null, the config is taken from the current instance.
     * @return mixed
     * @throws Exception_Configuration
     */
    protected function getConfig(
        $config_name,
        $instance = null
    ) {
        $current_instance = $this->instance;
        if (null !== $instance) {
            $current_instance = $instance;
        }

        return Config::getInstance($current_instance)->getConfig($config_name);
    }

    /**
     * Customize this method in your controller to define which 'expected' params can you receive by GET.
     * Use array( url_param_code => array( 'internal_key' => 'param_name',  // mandatory
     *                                'is_list'         => true/false, // mandatory
     *                                'apply_translation' => true/false // mandatory
     *                                'accepted_values' => array( list_values ), // optional
     *                             ),
     *            );
     * Where url_param_code is a char.
     *
     * @return array
     */
    protected function getParamsDefinition()
    {
        return [];
    }

    /**
     * Parse the url params in params array searching for some expected params. If someone is found modify the array.
     * $params array is referenced.
     *
     * @return array
     * @throws Exception_404
     */
    protected function parseParams()
    {
        $expected_params = [];
        $expected_url_params = $this->getParamsDefinition();

        if (empty($expected_url_params)) {
            // The controller didn't declare any parameters, nothing to do.
            return [];
        }

        // Build "reverse" keys array.
        foreach ($expected_url_params as $key => $value) {
            $expected_url_keys[$value['internal_key']] = $key;

            // Check if a "default_value" is used when there aren't any parameters in the URL.
            if (isset($expected_url_params[$key]['default_value'])) {
                $expected_params[$key] = ($expected_url_params[$key]['default_value']);
            }
        }

        if (empty($expected_params) && (empty($this->params['params']) || empty($expected_url_params))) {
            return [];
        }

        // Expected params:
        $max = (count($this->params['params']) - 1); // -1 because the last param never is a param key and to avoid asking a non existing key.

        if (count($expected_url_params)) {
            for ($i = 0; $i < $max; $i++) {
                $param = $this->params['params'][$i];

                if (
                    (3 >= strlen($param)) &&  // Can be a expected key
                    (filter_var($param, FILTER_DEFAULT)) && // Is a valid string
                    (array_key_exists($param, $expected_url_keys)) && // Exists in getParamsDefinition params array.
                    (false !== filter_var($this->params['params'][$i + 1], FILTER_DEFAULT) && '' != filter_var(
                            $this->params['params'][$i + 1],
                            FILTER_DEFAULT
                        )) // The value is a valid string and is not empty.
                ) {
                    $value = $this->params['params'][$i + 1];

                    if ($expected_url_params[$expected_url_keys[$param]]['is_list']) {
                        $value = explode(',', $value);
                    }

                    if ($expected_url_params[$expected_url_keys[$param]]['apply_translation']) {
                        // Save current domain:
                        $current_domain = $this->i18n->getDomain();

                        $this->i18n->setDomain('urlparams', $this->language);
                        if (is_array($value)) {
                            // Try to translate all the params.
                            foreach ($value as &$item) {
                                $item = $this->i18n->getReverseTranslation($item);
                            }
                        } else {
                            $value = $this->i18n->getReverseTranslation($value);
                        }

                        // Restore current domain:
                        $this->i18n->setDomain($current_domain, $this->language);
                    }

                    if (isset($expected_url_params[$expected_url_keys[$param]]['accepted_values'])) {
                        if (is_array($value) && count(array_diff($value, $expected_url_params[$expected_url_keys[$param]]['accepted_values']))) {
                            throw new Exception_404('The value passed in the parameters is not included in the "accepted_values"');
                        }

                        if (!is_array($value) && (!in_array($value, $expected_url_params[$expected_url_keys[$param]]['accepted_values']))) {
                            throw new Exception_404('The value passed is the parameters is not included in the "accepted_values"');
                        }
                    }

                    $expected_params[$expected_url_keys[$param]] = $value;
                    unset($this->params['params'][$i]); // Clean param
                    unset($this->params['params'][++$i]); // Clean value
                }
            }
        }

        return $expected_params;
    }

    /**
     * Returns the select page from url. Default returns 1 (1st)
     *
     * @return integer
     */
    protected function getCurrentPage()
    {
        // Functions as is_numeric do not work properly with large integers in 64bit machines.
        if (!empty($this->params['params']) && preg_match('/^[0-9]+$/', end($this->params['params']))) {
            return array_pop($this->params['params']);
        }

        return 1;
    }

    /**
     * Returns whether the debug is available or not.
     *
     * @return boolean
     * @deprecated Please use Domains::getInstance()->getDebugMode() instead.
     */
    public function hasDebug()
    {
        return Domains::getInstance()->getDebugMode();
    }

    /**
     * Change instance environment. It changes the hole instance configuration in runtime process.
     * @param string $instance
     * @param string $domain
     * @param string $language
     * @param string $i18n_messages
     * @throws Exception_404
     * @throws Exception_500
     */
    public function changeInstanceEnvironment(
        $instance,
        $domain,
        $language,
        $i18n_messages = 'messages'
    ) {
        Bootstrap::$instance = $instance;
        Domains::getInstance()->changeDomain($domain);
        I18N::setDomain($i18n_messages, $language, $instance);
        $this->__construct();
    }

    private function checkKillSession()
    {
        if (Domains::getInstance()->getDebugMode() && (FilterGet::getInstance()->getInteger('kill_session'))) {
            @Session::getInstance()->destroy();
        }
    }

    private function addJsonHeadersIfRequired()
    {
        if (!$this->is_json) {
            return;
        }

        if (!empty(FilterGet::getInstance()->getString('json_callback'))) {
            Headers::set('Content-type', 'text/javascript');
            return;
        }

        Headers::set('Content-type', 'application/json');
    }

    /**
     * @param $cached_content
     * @throws ControllerException
     */
    private function outputCachedContent($cached_content)
    {
        if ($cached_content instanceof Exception) {
            throw new ControllerException("Controller Build has generated an exception (cached).", null, $cached_content);
        }
        $this->postDispatch();
        $cached_content = $this->realTimeReplacement($cached_content);
        Headers::send();

        if (extension_loaded('newrelic')) {
            newrelic_name_transaction($this->params['controller']);
        }

        $this->outputContent($cached_content);
    }

    /**
     * @param $return
     * @return false|string
     * @throws ControllerException
     * @throws Exception_DependencyInjector
     */
    private function grabJson($return)
    {
        if (Domains::getInstance()->getDebugMode() && is_array($return)) {
            $this->stopBench(self::BENCHMARK_PARENT_KEY, "----- TOTAL " . get_class($this) . " + PREVIOUS MODULES -----");
            Debug::subSet('controllers', get_class($this), $this->debug_info);
            $return['debug_total_time'] = Benchmark::getInstance()->timingCurrent();

            $this->getModuleOutput('DebugIndex', ['show_debug_timers' => false, 'executed_controller_is_json' => true]);

            $return['debug_execution_key'] = Debug::getExecutionKey();
        }

        $json_callback = FilterGet::getInstance()->getString('json_callback');
        $content = ($json_callback ? $json_callback . '(' . json_encode($return) . ')' : json_encode($return));
        return $content;
    }
}

class ControllerException extends Exception
{
}
