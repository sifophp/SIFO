<?php

namespace Sifo;

$is_defined_in_vhost = (false !== ini_get('newrelic.appname') && 'PHP Application' !== ini_get('newrelic.appname'));
if (!$is_defined_in_vhost && extension_loaded('newrelic') && isset($instance)) {
    newrelic_set_appname(ucfirst($instance));
}

require_once ROOT_PATH . '/vendor/sifophp/sifo/src/Sifo/Exceptions.php';
require_once ROOT_PATH . '/vendor/sifophp/sifo/src/Sifo/Config.php';
require_once ROOT_PATH . '/vendor/autoload.php';

class Bootstrap
{
    /** @var string */
    public static $root;
    /** @var string */
    public static $application;
    /** @var string */
    public static $instance;
    /** @var string */
    public static $language;
    /** @var string */
    public static $controller;
    /** @var DependencyInjector */
    public static $container;

    /**
     * Starts the execution. Root path is passed to avoid recalculation.
     *
     * @param $instance_name
     * @param string $controller_name Optional, a controller to execute. If null the router will be used to determine it.
     *
     * @throws ControllerException
     * @throws DomainsException
     * @throws Exception_500
     * @throws Exception_Configuration
     * @throws Exception_DependencyInjector
     * @throws Headers_Exception
     * @internal param string $root Path to root.
     */
    public static function execute(
        $instance_name,
        $controller_name = null
    ) {
        self::$root = ROOT_PATH;
        self::$application = dirname(__FILE__);
        self::$instance = $instance_name;
        self::$container = DependencyInjector::getInstance();

        Benchmark::getInstance()->timingStart();

        self::dispatch($controller_name);

        Benchmark::getInstance()->timingStop();
    }

    /**
     * Invokes a controller with the folder/action form.
     *
     * @param string $controller The controller in folder/action form.
     *
     * @return Controller
     * @throws Exception_DependencyInjector
     */
    public static function invokeController($controller)
    {
        $class = self::convertToControllerClassName($controller);

        if (self::$container->has($class)) {
            $controller = self::$container->get($class);
        } else {
            /** @var Controller $controller */
            $controller = new $class();
        }

        $controller->setContainer(self::$container);

        return $controller;
    }

    private static function convertToControllerClassName($controller): string
    {
        if (class_exists($controller)) {
            return $controller;
        }

        $controller_path = explode('/', $controller);

        $class = '';
        foreach ($controller_path as $part) {
            $class .= ucfirst($part);
        }

        $class .= 'Controller';

        $instance_inheritance = array_reverse(Domains::getInstance()->getInstanceInheritance());
        foreach ($instance_inheritance as $instance) {
            $controller_classname = '\\' . ucfirst($instance) . '\\' . $class;
            if (class_exists($controller_classname)) {
                $class = $controller_classname;
                break;
            }
        }

        return $class;
    }

    /**
     * Sets the controller and view properties and executes the controller, sending the output buffer.
     *
     * @param string $controller Dispatches a specific controller, or use URL to determine the controller
     * @throws ControllerException
     * @throws DomainsException
     * @throws Exception_500
     * @throws Exception_Configuration
     * @throws Exception_DependencyInjector
     * @throws Headers_Exception
     */
    public static function dispatch($controller = null)
    {
        try {
            self::checkDomainRedirect();
            self::checkDomainAuthentication();
            self::checkIfDomainIsValid();
            self::setLanguage();
            self::overWritePHPini();

            $controller = $controller ?? (new Router(
                    Urls::getInstance(self::$instance)->getPathParts()[0],
                    self::$instance,
                    Domains::getInstance()->getSubdomain(),
                    self::$language,
                    Domains::getInstance()->www_mode
                ))->getController();

            $ctrl = self::invokeController($controller);
            $ctrl->addParams(['controller_route' => $controller]);
            self::$controller = $controller;

            self::enableDebugFeatures();

            $ctrl->dispatch();

            if (false === $ctrl->is_json && Domains::getInstance()->getDebugMode()) {
                self::invokeController('debug/index')->dispatch();
            }
        } // Don't know what to do after Domain is evaluated. Goodbye:
        catch (DomainsException $d) {
            Headers::setResponseStatus(404);
            Headers::send();
            echo "<h1>{$d->getMessage()}</h1>";
            die;
        } catch (ControllerException $e) {
            self::_dispatchErrorController($e->getPrevious());
        } catch (\Exception $e) {
            self::_dispatchErrorController($e);
        }
    }

    /**
     * Dispatches an error after an exception.
     *
     * @param \Exception $e
     *
     * @return mixed|void output buffer
     * @throws ControllerException
     * @throws DomainsException
     * @throws Exception_500
     * @throws Exception_Configuration
     * @throws Exception_DependencyInjector
     * @throws Headers_Exception
     */
    private static function _dispatchErrorController($e)
    {
        if (!isset($e->http_code)) {
            $e->http_code = 503;
            $e->http_code_msg = 'Exception!';
            $e->redirect = false;
        }

        Headers::setResponseStatus($e->http_code);
        Headers::send();

        // Execute ErrorCommonController when an exception is captured.
        $ctrl2 = self::invokeController('error/common');

        // Set params:
        $ctrl2->addParams(
            [
                'code' => $e->http_code,
                'code_msg' => $e->http_code_msg,
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]
        );

        // All the SEO_Exceptions with need of redirection have this attribute:
        if ($e->redirect) {
            // Path is passed via message:
            $path = trim($e->getMessage(), '/');
            // Check if the URL for the redirection has already a protocol, like http:// , https://, ftp://, etc..
            if (false !== strpos($path, '://')) {
                // Absolute path passed:
                $new_location = $path;
            } else {
                // Relative path passed, use path as the key in url.config.php file:
                $new_location = Urls::getUrl($path);
            }

            if (empty($new_location) || false == $new_location) {
                trigger_error("Exception " . $e->http_code . " raised with an empty location " . $e->getTraceAsString());
                Headers::setResponseStatus(500);
                Headers::send();
                exit;
            }

            if (!Domains::getInstance()->getDebugMode()) {
                Headers::setResponseStatus($e->http_code);
                Headers::set('Location', $new_location, $e->http_code);
                Headers::send();
                return;
            } else {
                $ctrl2->addParams(['url_redirect' => $new_location]);
                $ctrl2->dispatch();
                self::invokeController('debug/index')->dispatch();
                return;
            }
        }

        $ctrl2->dispatch();

        if (Domains::getInstance()->getDebugMode()) {
            self::invokeController('debug/index')->dispatch();
        }
    }

    /**
     * @throws DomainsException
     * @throws Exception_500
     * @throws Exception_Configuration
     */
    private static function overWritePHPini()
    {
        $php_ini = Domains::getInstance()->getPHPInis();

        if (!$php_ini) {
            return;
        }

        foreach ($php_ini as $varname => $newvalue) {
            ini_set($varname, $newvalue);
        }
    }

    /**
     * @throws DomainsException
     * @throws Exception_500
     * @throws Exception_Configuration
     * @throws Headers_Exception
     */
    private static function checkDomainRedirect(): void
    {
        $destination = Domains::getInstance()->getRedirect();
        if (!empty($destination)) {
            Headers::setResponseStatus(301);
            Headers::set('Location', $destination, 301);
            Headers::send();
            exit;
        }
    }

    /**
     * @throws DomainsException
     * @throws Exception_401
     * @throws Exception_500
     * @throws Exception_Configuration
     */
    private static function checkDomainAuthentication(): void
    {
        $auth_data = Domains::getInstance()->getAuthData();

        if (!empty($auth_data) && FilterCookie::getInstance()->getString('domain_auth') != $auth_data['hash']) {
            $filter_server = FilterServer::getInstance();
            if ($filter_server->isEmpty('PHP_AUTH_USER') || $filter_server->isEmpty('PHP_AUTH_PW') || $filter_server->getString(
                    'PHP_AUTH_USER'
                ) != $auth_data['user'] || $filter_server->getString('PHP_AUTH_PW') != $auth_data['password']
            ) {
                Headers::set('WWW-Authenticate', 'Basic realm="Protected page"');
                Headers::send();
                throw new Exception_401('You should enter a valid credentials.');
            }

            // If the user is authorized, we save a session cookie to prevent multiple auth under subdomains in the same session.
            setcookie('domain_auth', $auth_data['hash'], 0, '/', Domains::getInstance()->getDomain());
        }
    }

    private static function setLanguage(): void
    {
        self::$language = Domains::getInstance()->getLanguage();
    }

    /**
     * @throws DomainsException
     * @throws Exception_404
     * @throws Exception_500
     * @throws Exception_Configuration
     */
    private static function checkIfDomainIsValid(): void
    {
        if (!Domains::getInstance()->valid_domain) {
            throw new Exception_404('Unknown language in domain');
        }
    }

    /**
     * @throws DomainsException
     * @throws Exception_500
     * @throws Exception_Configuration
     */
    private static function enableDebugFeatures(): void
    {
        if (Domains::getInstance()->getDevMode()) {
            if (FilterGet::getInstance()->getInteger('clean_compile')) {
                $smarty_compiles_dir = ROOT_PATH . "/instances/" . self::$instance . "/templates/_smarty/compile/*";
                system('rm -rf ' . $smarty_compiles_dir);
            }
            if (FilterGet::getInstance()->getInteger('rebuild_all')) {
                Cookie::set('rebuild_all', 1);
            }
            if (FilterGet::getInstance()->getInteger('rebuild_nothing') && FilterCookie::getInstance()->getInteger('rebuild_all')) {
                Cookie::delete('rebuild_all');
            }
            if (1 === FilterGet::getInstance()->getInteger('debug')) {
                Cookie::set('debug', 1);
            }
            if (0 === FilterGet::getInstance()->getInteger('debug')) {
                Cookie::set('debug', 0);
            }
            if (false !== ($debug = FilterCookie::getInstance()->getInteger('debug'))) {
                Domains::getInstance()->setDebugMode((bool)$debug);
            }
        }
    }
}
