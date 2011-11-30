<?php
/**
 * LICENSE
 *
 * Copyright 2010 Albert Lombarte
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Sifo;

abstract class Controller
{
	/**
	 * Cache expiration for this controller, by default set to 4 hours (in seconds).
	 *
	 * @var integer
	 */
	const CACHE_DEFAULT_EXPIRATION = 14400;

	/**
	 * Use compression in caching?
	 */
	const CACHE_COMPRESS = 0;

	/**
	 * Define the format of the stored cache tag.
	 *
	 * @var string
	 */
	const CACHE_TAG_STORE_FORMAT = '!tag-%s=%s';

	/**
	 * List of classes that will be autoloaded automatically.
	 *
	 * Format: $include_classes = array( 'Metadata', 'FlashMessages', 'Session', 'Cookie' );
	 */
	protected $include_classes = array();

	/**
	 * Stores the final cache expiration in seconds. This param cannot be initialized to a default value.
	 *
	 * @var integer
	 */
	protected $cache_expiration;

	/**
	 * Information useful for debugging.
	 *
	 * @var array
	 */
	protected $debug_info = array();

	/**
	 * Whether the page has debug or not.
	 *
	 * @var boolean
	 */
	static private $has_debug = false;

	/**
	 * Associated modules being executed as dependencies.
	 *
	 * @var array
	 */
	protected $modules = array();

	/**
	 * Flag controlling if the controller should behave like a normal HTML or as json response.
	 *
	 * By setting this value to true, the debug will be disabled (would break the json format).
	 *
	 * @var boolean
	 */
	public $is_json = false;

	/**
	 * Parameters used in the controller.
	 *
	 * @var array
	 */
	protected $params;

	/**
	 * Language used in this controller. Eg.: en_US.
	 *
	 * @var string
	 */
	protected $language;

	/**
	 * View object
	 *
	 * @var View
	 */
	private $view;

	/**
	 * I18n object
	 *
	 * @var I18N
	 */
	public $i18n;

	/**
	 * Template used as layout.
	 *
	 * @var string
	 */
	protected $layout;

	abstract function build();

	public function __construct()
	{

		$this->includeClasses();
		$this->instance = Bootstrap::$instance;
		$this->language = Domains::getInstance()->getLanguage();

		$this->url_definition = Urls::getInstance( $this->instance )->getUrlDefinition();
		self::$has_debug = Bootstrap::$debug;

		$urls = Urls::getInstance( $this->instance )->getUrlConfig();
		$current_url = $this->getUrl( Urls::getInstance( Bootstrap::$instance )->getPath(), Urls::getInstance( $this->instance )->getParams() );

		$urls['current_url'] = $current_url;
		$this->params = array(
				// Sanitizes the current URL in case is dirty:
				'current_url' => $current_url,
				'instance' => Bootstrap::$instance,
				'controller' => get_class( $this ),
				'path' => Urls::getInstance( $this->instance )->getPath(),
				'path_parts' => Urls::getInstance( $this->instance )->getPathParts(),
				'params' => Urls::getInstance( $this->instance )->getParams(),
				'has_debug' => Bootstrap::$debug,
				'lang' => $this->language,
				'url' => $urls,

				// 'definition' => $this->url_definition, // In case you want to see the URL definition
		);

		// Init i18n configuration.
		$this->i18n = I18N::getInstance( Domains::getInstance()->getLanguageDomain(), $this->language );

		// Parse Key-Value parameters
		$this->params['parsed_params'] = $this->parseParams();
		$this->params['page'] = $this->getCurrentPage();

		$this->view = new View();
	}

	public function getAssignedVars()
	{
		return $this->view->getTemplateVars();
	}

	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Performs the form validation workflow.
	 *
	 * @param type $submit_button
	 * @param type $form_config
	 * @return null if no submit sent. True if validated correctly, false otherwise.
	 */
	protected function getValidatedForm( $submit_button, $form_config, $default_fields = array() )
	{
		$post = FilterPost::getInstance();

		$form = new Form( $post, $this->view );
		if ( $post->isSent( $submit_button ) )
		{
			$return = $form->validateElements( $form_config );

			if ( $return )
			{
				$return = $form;
			}
		}
		else
		{
			$form->addFields( $default_fields );
			$return = null;
		}

		$this->assign( 'form_values', $form->getFields() );
		$this->assign( 'requirements', $form->getRequirements( $form_config ) );
		$this->assign( 'error', $form->getErrors() );

		return $return;
	}

	/**
	 * Includes all the classes passed in the 'include_classes' attribute.
	 */
	protected function includeClasses()
	{
		if ( is_array( $this->include_classes ) && !empty ( $this->include_classes ) )
		{
			foreach ( $this->include_classes as $class )
			{
				$this->getClass( $class, false );
			}
		}
	}

	public function getUrl( $relative_path, $params = null )
	{
		$url = Urls::getInstance( $this->instance )->getUrl( $relative_path );
		if ( ( !$url ) &&  ( '' != $relative_path ) )
		{
			/*
			 * The relative_path exists but the first getUrl try not found it.
			 * We try with the path translations:
			*/
			if ( Router::getReversalRoute( $relative_path ) )
			{
				if ( !( $url = Urls::getInstance( $this->instance )->getUrl( Router::getReversalRoute( $relative_path ) ) ) )
				{
					// Fixed the current_url in url like word1_word2 for url word1-word2
					$url = Urls::getInstance( $this->instance )->getUrl( Router::getReversalRoute( str_replace( '-','_', $relative_path ) ) );
				}
			}
			else
			{
				$url = Urls::$base_url . '/' . $relative_path;
			}
		}

		if ( $params )
		{
			$url .= $this->url_definition['params_separator'];

			if ( is_array( $params ) )
			{
				$url .= implode( $this->url_definition['params_separator'], $params );
			}
			else
			{
				$url .= $params;
			}
		}

		return $url;
	}

	/**
	 * Stops the execution of the current controller in order to dispatch the given controller.
	 *
	 * @param string $controller Controller in the format 'folder/file'
	 */
	public function reDispatch( $controller )
	{
		Bootstrap::dispatch( $controller );
		exit;
	}

	/**
	 * Sets a template (relative path) as the template that triggers the page.
	 *
	 * @param string $template
	 */
	public function setLayout( $template )
	{
		$this->layout = $this->getTemplate( $template );
	}

	/**
	 * Returns the absolute path to a template. Customized templates are specified in the configuration files.
	 *
	 * @param string $template
	 */
	public function getTemplate( $template )
	{
		return ROOT_PATH . '/' . Config::getInstance( $this->instance )->getConfig( 'templates', $template ) ;
	}

	/**
	 * Assign a variable to the template.
	 *
	 * @param string|array $tpl_var
	 * @param mixed $value
	 * @return void
	 */
	public function assign( $tpl_var, $value )
	{
		if ( $tpl_var != 'modules' )
		{
			$this->addToDebug( $tpl_var, $value, 'assigns' );
		}
		return $this->view->assign( $tpl_var, $value );
	}

	/**
	 * Executes all the modules setted previously with the addModule method.
	 */
	protected function executeNestedModules()
	{
		if ( count( $this->modules ) > 0 )
		{
			// Execute additional modules and put their result in the 'modules' variable.
			foreach ( $this->modules as $module_name => $controller )
			{
				$modules[$module_name] = $this->dispatchSingleController( $controller, array( 'module_name' => $module_name ) );
			}
			unset( $this->modules );

			$this->assign( 'modules', $modules );
		}
	}

	/**
	 * Dispatch the controller.
	 */
	public function dispatch()
	{
		if ( $this->hasDebug() && ( FilterGet::getInstance()->getInteger( 'kill_session' ) ) )
		{
			$this->getClass( 'Session', false );
			@Session::getInstance()->destroy();
		}

		if ( $this->is_json )
		{
			// Set headers before cache:
			if ( $json_callback = FilterGet::getInstance()->getString( 'json_callback' ) )
			{
				header( 'Content-type: text/javascript' );
			}
			else
			{
				header( 'Content-type: application/json' );
			}
		}

		$benchmark_key = 'controller_execution_time_parent';
		$this->startBench( $benchmark_key );

		$this->preDispatch();
		$cached_content = $this->grabCache();
		if ( false !== $cached_content )
		{
			$this->postDispatch();
			$cached_content = $this->_realTimeReplacement( $cached_content );
			echo $cached_content;
			return;
		}

		$cache_key = $this->parseCache();

		if ( false !== $cache_key )
		{
			$this->addToDebug( 'name', $cache_key['name'], 'Cache properties' );
			$this->addToDebug( 'expiration', $cache_key['expiration'], 'Cache properties' );
		}


		$return = $this->build();
		$controller_params = array_merge( array( 'layout' => $this->layout ), $this->getParams() );
		$this->addToDebug( 'parameters', $controller_params, 'CONTROLLER' );
		$this->executeNestedModules();

		if ( $this->is_json )
		{
			$json_callback = FilterGet::getInstance()->getString( 'json_callback' );
			$content = ( $json_callback ? $json_callback . '(' . json_encode( $return ) . ')':	json_encode( $return ) );
		}
		else
		{
			$content =  $this->grabHtml();
		}

		if ( false !== $cache_key )
		{
			Cache::getInstance()->set( $cache_key['name'], $content, self::CACHE_COMPRESS, $cache_key['expiration'] );
		}

		$this->postDispatch();
		$this->stopBench( $benchmark_key, "----- TOTAL " .get_class( $this ) . " + PREVIOUS MODULES -----" );

		$content = $this->_realTimeReplacement( $content );

		echo $content;
	}

	/**
	 * Grabs the HTML for a smarty template.
	 *
	 * @return html
	 */
	protected function grabHtml()
	{
		$class_name = get_class( $this );
		if ( !$this->is_json && !isset( $this->layout ) )
		{
			throw new Exception_500( 'Layout not set in controller ' . $class_name );
		}

		$this->startBench( "view_$class_name" );
		// Assign common vars:
		$this->assignCommonVars();

		// Add another key inside the debug key:
		Registry::getInstance()->subSet( 'debug', $class_name, $this->debug_info );
		$content = $this->view->fetch( $this->layout );
		$this->stopBench( "view_$class_name", "$class_name: Smarty fetch" );
		return $content;
	}

	/**
	 * Returns tha contents in cache or false.
	 *
	 * @return mixed
	 */
	protected function grabCache()
	{
		if ( Domains::getInstance()->getDevMode() && ( FilterCookie::getInstance()->getInteger( 'rebuild_all' ) || FilterGet::getInstance()->getInteger( 'rebuild' ) ) )
		{
			return false;
		}

		// When DATA is sent, invalidate cache:
		if ( 0 < FilterPost::getInstance()->countVars() )
		{
			return false;
		}

		$cache_key = $this->parseCache();
		// Controller does not uses cache:
		if ( !$cache_key )
		{
			return false;
		}

		// The hasExpired method is only for cache disk. Cache system have its own TTL
		if ( Cache::getInstance() instanceof CacheDisk && Cache::getInstance()->hasExpired( $cache_key['name'], $cache_key['expiration'] ) )
		{
			return false;
		}

		Benchmark::getInstance()->timingStart( 'cache' );
		$content = Cache::getInstance()->get( $cache_key['name'] );
		Benchmark::getInstance()->timingCurrentToRegistry( 'cache' );

		if ( $content )
		{
			// Add another key inside the debug key:
			Registry::getInstance()->subSet( 'debug', get_class( $this ). ' <small>- Retrieved from ' . Cache::$cache_type . ' ['.$cache_key['name'] . ']</small>', $this->debug_info );
			return $content;
		}

		return false; // Life was beautiful, but although everything seemed to be cached, it wasn't.
	}

	/**
	 * Parses the cache function to determine name and expiration.
	 *
	 * All caches managed by controller pass this point.
	 *
	 * @return array
	 */
	protected function parseCache()
	{
		$cache_key = $this->getCacheDefinition();

		if ( false === $cache_key || '' === $cache_key  )
		{
			return false;
		}

		if ( !is_array( $cache_key ) )
		{
			$cache_key = array( 'cachekey' => $cache_key );
		}

		$cache_key['expiration'] = self::CACHE_DEFAULT_EXPIRATION;
		if ( !empty( $this->cache_expiration ) )
		{
			$cache_key['expiration'] = $this->cache_expiration;
		}

		// Prepend necessary values to cache:
		$cache_key['name'] = $this->_getFinalCacheKeyName( $cache_key );

		return $cache_key;
	}

	/**
	 * Returns the final cache name, prepending the necessary attributes.
	 *
	 * @param array $definition Cache definition.
	 * @return string
	 */
	private function _getFinalCacheKeyName( Array $definition )
	{
		$cache_key = array();
		$cache_base_key = array();

		// First of all, let's construct the cache base with domain, language and controller name.
		$cache_base_key[] = Domains::getInstance()->getDomain();
		$cache_base_key[] = $this->language;
		$cache_base_key[] = get_class( $this );

		// Now we add the rest of identifiers of the definition excluding the "expiration".
		unset( $definition['expiration'] );

		if ( !empty( $definition ) )
		{
			foreach ( $definition as $key => $val )
			{
				$cache_key[] = $this->getCacheTag( $key, $val );
			}
			sort( $cache_key );
		}

		return implode( '_', array_merge( $cache_base_key, $cache_key ) );
	}

	/**
	 * Construct the cache tag if it's defined in config.
	 *
	 * @param string $tag Cache tag.
	 * @param mixed $value Cache value.
	 * @return string
	 */
	protected function getCacheTag( $tag, $value )
	{
		$cache_tag = $tag . '=' . $value;

		$cache_config = Config::getInstance()->getConfig( 'memcache' );
		if ( isset( $cache_config['cache_tags'] ) && in_array( $tag, $cache_config['cache_tags'] ) )
		{
			$pointer = Cache::getInstance()->get( sprintf( self::CACHE_TAG_STORE_FORMAT, $tag, $value ) );
			$cache_tag .= '/' . ( int ) $pointer;
		}

		return $cache_tag;
	}

	/**
	 * Returns the cache definition of this controller.
	 *
	 * A string with the cache key can be returned or an array with 'name' and 'expiration' (both mandatory).
	 *
	 * @return string|array|false
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
	 */
	public function deleteCache( $key_definition )
	{
		if ( !is_array( $key_definition ) )
		{
			$key_definition = array( 'name' => $key_definition );
		}

		return Cache::getInstance()->delete( $this->_getFinalCacheKeyName( $key_definition ) );
	}

	/**
	 * Delete cache from all the controllers that contain the given tag.
	 *
	 * @param string $tag Cache tag.
	 * @param mixed $value Cache value.
	 * @return boolean
	 */
	public function deleteCacheByTag( $tag, $value )
	{
		$stored_tag = sprintf( self::CACHE_TAG_STORE_FORMAT, $tag, $value );
		$cache_handler = Cache::getInstance();

		if ( false === $cache_handler->add( $stored_tag, 1 ) )
		{
			$cache_handler->increment( $stored_tag );
		}

		return true;
	}

	protected function assignCommonVars()
	{
		$this->assign( 'url', $this->params['url'] );
		$this->assign( '_tpls', Config::getInstance()->getConfig( 'templates' ) );
	}

	/**
	 * Returns the HTML of a smarty template or false if was impossible to fetch.
	 *
	 * @param string $template
	 * @return boolean
	 */
	public function fetch( $template )
	{
		try
		{
			$template = $this->getTemplate( $template );
			$this->assignCommonVars();
			return $this->view->fetch( $template );
		}
		catch( Exception_Configuration $e )
		{
			return false;
		}

	}

	/**
	 * Executes the current controller.
	 *
	 * @return string HTML output.
	 */
	public function execute()
	{
		$this->build();
		$controller_params = array_merge( array( 'layout' => $this->layout ), $this->getParams() );
		$this->addToDebug( 'parameters', $controller_params, 'CONTROLLER' );
		return $this->grabHtml();
	}


	/**
	 * After the content is rendered all the tags <!-- REPLACED are searched for module execution.
	 *
	 * This function allows to decrease the number of memcache sets.
	 *
	 * @param string $buffer HTML output.
	 * @return string HTML output.
	 */
	private function _realTimeReplacement( $buffer )
	{
		$benchmark_key = 'controller_execution_replace';
		$this->startBench( $benchmark_key );

		// Only letters, numbers _ and . ALLOWED. Take care with parameters.
		$buffer = preg_replace_callback( '/<\!--\s*REPLACE\:([a-zA-Z0-9:_\.\-,\/]*)\s*-->/', array( $this, '_executeReplacementModule' ), $buffer );

		$this->stopBench( $benchmark_key, "---- TOTAL REALTIME REPLACEMENTS ----" );
		return $buffer;
	}

	/**
	 * Executes a module requested in the <!-- REPLACE --> tag and returns its output.
	 *
	 * @param array $matches Preg_replace matches.
	 * @return string HTML for the replaced module.
	 */
	private function _executeReplacementModule( $matches )
	{
		// Take params set by tag <!-- REPLACE -->:
		$replace_params = explode( '::', $matches[1] );
		$controller = $replace_params[0];
		unset ( $replace_params[0] );
		return $this->dispatchSingleController( $controller, array( 'params' => array_values( $replace_params ) ) );
	}

	/**
	 * Dispatch a single controller. Fetch from cache (if any), execute, store cache, return output.
	 *
	 * @param string $controller Name of controller to execute.
	 * @param array $params Additional parameters needed by the controller
	 * @return string
	 */
	public function dispatchSingleController( $controller, $params = array() )
	{
		$benchmark_key = 'controller_execution_time';
		$this->startBench( $benchmark_key );

		$module = Bootstrap::invokeController( $controller );
		$module->setParams( array_merge( $this->getParams(), $params ) );
		$module->preDispatch();
		$cached_content = $module->grabCache();
		$class_name = get_class( $module );

		if ( false !== $cached_content )
		{
			$module_content = $cached_content;
		}
		else
		{
			$cache_key = $module->parseCache();
			if ( false !== $cache_key )
			{
				$module->addToDebug( 'name', $cache_key['name'], 'Cache properties' );
				$module->addToDebug( 'expiration', $cache_key['expiration'], 'Cache properties' );
			}
			$module_content = $module->execute();
		}

		$cache_key = $module->parseCache();

		if ( false !== $cache_key )
		{
			Cache::getInstance()->set( $cache_key['name'], $module_content, self::CACHE_COMPRESS, $cache_key['expiration'] );
		}

		$module->postDispatch();
		$this->stopBench( $benchmark_key, "$class_name: TOTAL module execution" );

		return $module_content;
	}

	/**
	 * Actions executed BEFORE the controller is dispatched or cache is called.
	 *
	 * @return void
	 */
	public function preDispatch() {}

	/**
	 * Actions executed AFTER the controller has been dispatched and cache fetched and right before the output is sent to browser.
	 *
	 */
	public function postDispatch() {}

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
	public function getParam( $param_name )
	{
		if ( isset( $this->params[$param_name] ) )
		{
			return $this->params[$param_name];
		}

		return false;
	}

	/**
	 * Return a param results from expected definition parse.
	 *
	 * @param string $param_name
	 * @return mixed False if not found.
	 */
	public function getParsedParam( $param_name )
	{
		if ( !empty( $this->params['parsed_params'] ) && isset( $param_name, $this->params['parsed_params'], $this->params['parsed_params'][$param_name] ) )
		{
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
	public function getUrlParam( $number )
	{
		if ( isset( $this->params['params'][$number] ) )
		{
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
	public function translate( $subject, $var_1 = '', $var2 = '', $var_n = '' )
	{
		$args = func_get_args();

		$variables = array();
		if ( 1 < count( $args ) )
		{
			foreach ( $args as $key => $value )
			{
				$variables['%'.$key] = $value;
			}

		}

		unset( $variables['%0'] );
		return $this->i18n->getTranslation( $subject, $variables );
	}

	/**
	 * Reset the parameters of the controller with the new ones passed.
	 *
	 * @param array $params
	 */
	public function setParams( $params )
	{
		$this->params = $params;
	}

	/**
	 * Add new parameters to the params array on the controller.
	 *
	 * @param array $params
	 */
	public function addParams( Array $params )
	{
		$this->params = array_merge( $this->params, $params );
	}

	/**
	 * Adds a module in the battery to be executed later.
	 *
	 * @param string $controller
	 */
	public function addModule( $name, $controller )
	{
		$this->modules[$name] = $controller;
	}

	/**
	 * Add modules in the battery from an array.
	 *
	 * @param array $modules List of modules to load.
	 */
	public function addModules( array $modules = array() )
	{
		foreach ( $modules as $key => $val )
		{
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
	 * Add JS to the stack.
	 *
	 * @param string $media_name Name of the JS file.
	 */
	protected function addJs( $media_name )
	{
		$this->addMedia( 'js', $media_name );
	}

	/**
	 * Add CSS to the stack.
	 *
	 * @param string $media_name Name of the CSS file.
	 */
	protected function addCss( $media_name )
	{
		$this->addMedia( 'css', $media_name );
	}

	/**
	 * Add some kind of media to the stack to be loaded in the head.
	 *
	 * @param string $media_type Media type [js|css].
	 * @param string $group_name Name of the group in the js|css config file.
	 */
	protected function addMedia( $media_type, $group_name )
	{
		$media = $this->getParam( 'media' );

		if ( !isset( $media[$media_type] ) || !in_array( $group_name, $media[$media_type] ) )
		{
			$media_config = $this->getConfig( $media_type );
			if ( isset( $media_config['packages'][$group_name] ) )
			{
				$media[$media_type][key( $media_config['packages'][$group_name] )] = $group_name;
				ksort( $media[$media_type] );
			}
			else
			{
				trigger_error( 'The specified group name "' . $group_name . '" does not exists in config file', E_USER_WARNING );
			}
		}

		$this->addParams( array( 'media' => $media ) );
	}

	/**
	 * Adds an element in the debug as a new entry. You can set the context to create groups.
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $context
	 */
	protected function addToDebug( $key, $value, $context = null)
	{
		// Store everything in the debug in the registry.
		if ( $this->hasDebug() )
		{
			if (  null === $context )
			{
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
	protected function startBench( $key )
	{
		Benchmark::getInstance()->timingStart( $key );
	}

	/**
	 * Stops the timer for the bench.
	 * @param string $key Identifier that you used to start the bench.
	 * @param string $label Text that will be shown in the benchmarks table.
	 */
	protected function stopBench( $key, $label )
	{
		Registry::getInstance()->subSet( 'benchmarks', $label, Benchmark::getInstance()->timingCurrent( $key ) );
	}

	/**
	 * Returns an object of the given class.
	 *
	 * @param string $class_name
	 * @param boolean $call_constructor If you want to return a 'new' instance or not. Set to false for singletons.
	 * @return Instance_of_a_Class
	 */
	public function getClass( $class_name, $call_constructor = true )
	{
		return Bootstrap::getClass( $class_name, $call_constructor );
	}

	/**
	 * Get config from the current instance or a given one.
	 *
	 * @param string $config_name Config name.
	 * @param string $instance If null, the config is taken from the current instance.
	 * @return Config
	 */
	protected function getConfig( $config_name, $instance = null )
	{
		$current_instance = $this->instance;
		if ( null !== $instance )
		{
			$current_instance = $instance;
		}

		return Config::getInstance( $current_instance )->getConfig( $config_name );
	}

	/**
	 * Returns whether the debug is available or not.
	 *
	 * @return boolean
	 */
	public function hasDebug()
	{
		return self::$has_debug && Bootstrap::$debug;
	}

	/**
	 * Sets/unsets the debug as available.
	 *
	 * @param boolean $value True or false.
	 */
	public function setDebug( $value )
	{
		self::$has_debug = (bool) $value;
	}

	/**
	 * Customize this method in your controller to define wich 'expected' params can you receive by GET.
	 * Use array( url_param_code => array( 'internal_key' => 'param_name',  // manadtory
	 *								'is_list'		 => true/false, // mandatory
	 *								'apply_translation' => true/false // mandatory
	 *								'accepted_values' => array( list_values ), // optional
	 *							 ),
	 *			);
	 * Where url_param_code is a char.
	 *
	 * @return array
	 */
	protected function getParamsDefinition()
	{
		return array();
	}

	/**
	 * Parse the url params in params array searching for some expected params. If someone is found modify the array.
	 * $params array is referenced.
	 *
	 * @param array $params Get params.
	 * @return array
	 */
	protected function parseParams()
	{
		$expected_params = array();
		$expected_url_params = $this->getParamsDefinition();

		if ( empty( $expected_url_params ) )
		{
			// The controller didn't declare any parameters, nothing to do.
			return array();
		}

		// Build "reverse" keys array.
		foreach ( $expected_url_params as $key => $value )
		{
			$expected_url_keys[$value['internal_key']] = $key;

			// Check if a "default_value" is used when there aren't any parameters in the URL.
			if ( isset( $expected_url_params[$key]['default_value'] ) )
			{
				$expected_params[$key] = ( $expected_url_params[$key]['default_value'] );
			}
		}

		if ( empty( $expected_params ) && ( empty( $this->params['params'] ) || empty( $expected_url_params ) ) )
		{
			return array();
		}

		// Expected params:
		$max = ( count( $this->params['params'] ) - 1 ); // -1 because the last param never is a param key and to avoid asking a non existing key.

		if ( count( $expected_url_params ) )
		{
			for ( $i=0; $i < $max; $i++ )
			{
				$param = $this->params['params'][$i];

				if (
					( 1 == strlen( $param ) ) &&  // Can be a expected key
					( filter_var( $param, FILTER_DEFAULT ) ) && // Is a valid string
					( array_key_exists( $param, $expected_url_keys ) ) && // Exists in getParamsDefinition params array.
					( false !== filter_var( $this->params['params'][$i + 1], FILTER_DEFAULT ) && '' != filter_var( $this->params['params'][$i + 1], FILTER_DEFAULT ) ) // The value is a valid string and is not empty.
					)
				{
					$value = $this->params['params'][$i+1];

					if ( $expected_url_params[$expected_url_keys[$param]]['is_list'] )
					{
						$value = explode( ',', $value );
					}

					if ( $expected_url_params[$expected_url_keys[$param]]['apply_translation'] )
					{
						// Save current domain:
						$current_domain = $this->i18n->getDomain();

						$this->i18n->setDomain( 'urlparams', $this->language );
						if ( is_array( $value ) )
						{
							// Try to translate all the params.
							foreach ( $value as &$item )
							{
								$item = $this->i18n->getReverseTranslation( $item );
							}
						}
						else
						{
							$value = $this->i18n->getReverseTranslation( $value );
						}

						// Restore current domain:
						$this->i18n->setDomain( $current_domain, $this->language );
					}

					if ( isset( $expected_url_params[$expected_url_keys[$param]]['accepted_values'] ) )
					{
						if ( is_array( $value ) && count( array_diff( $value, $expected_url_params[$expected_url_keys[$param]]['accepted_values'] ) ) )
						{
							throw new Exception_404( 'The value passed in the parameters is not included in the "accepted_values"' );
						}

						if ( !is_array( $value ) && ( !in_array( $value, $expected_url_params[$expected_url_keys[$param]]['accepted_values'] ) ) )
						{
							throw new Exception_404( 'The value passed is the parameters is not included in the "accepted_values"' );
						}
					}

					$expected_params[$expected_url_keys[$param]] = $value;
					unset( $this->params['params'][$i] ); // Clean param
					unset( $this->params['params'][++$i] ); // Clean value
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
		if ( empty( $this->params['params'] ) )
		{
			return 1;
		}

		$last_value = array_pop( $this->params['params'] );
		// Functions as is_numeric do not work properly with large integers in 64bit machines.
		if ( preg_match( '/^[0-9]+$/', $last_value ) )
		{
			return $last_value;
		}
		array_push( $this->params['params'], $last_value );
		return 1;
	}
}