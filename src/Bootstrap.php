<?php

namespace Sifo;

use Sifo\Container\DependencyInjector;
use Sifo\Exception\ConfigurationException;
use Sifo\Exception\ControllerException;
use Sifo\Exception\SifoHttpException;
use Sifo\Exception\UnknownDomainException;
use Sifo\Http\Cookie;
use Sifo\Http\Domains;
use Sifo\Http\Headers;
use Sifo\Http\Router;
use Sifo\Http\Urls;

$is_defined_in_vhost = (false !== ini_get('newrelic.appname') && 'PHP Application' !== ini_get('newrelic.appname'));
if (!$is_defined_in_vhost && extension_loaded('newrelic') && isset($instance))
{
    newrelic_set_appname(ucfirst($instance));
}

if (!defined('ROOT_PATH'))
{
    define('ROOT_PATH', dirname(realpath(__FILE__)) . '/..');
}

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

    public static function execute(string $instance_name, string $controller_name = null)
    {
        self::$root        = ROOT_PATH;
        self::$application = dirname(__FILE__);
        self::$instance    = $instance_name;

        self::autoload();

        Benchmark::getInstance()->timingStart();

        self::dispatch($controller_name);

        Benchmark::getInstance()->timingStop();
    }

    private static function autoload()
    {
        spl_autoload_register(array('\\Sifo\\Bootstrap', 'includeFile'));
        self::$container = DependencyInjector::getInstance();
    }

    public static function invokeController(string $controller): Controller
    {
        $controller_path = explode('/', $controller);

        $class = '';
        foreach ($controller_path as $part)
        {
            $class .= ucfirst($part);
        }

        $class .= 'Controller';

        /** @var Controller $controller */
        $controller = new $class;
        $controller->setContainer(self::$container);

        return $controller;
    }

    /**
     * Includes (include_once) the file corresponding to the passed passed classname.
     * It does not instantiate any object.
     *
     * This method must be public as it is used in external places, as unit-tests.
     *
     * @param string $classname
     *
     * @throws SifoHttpException
     * @return string The classname you asked for.
     */
    public static function includeFile($classname)
    {
        try
        {
            $class_info = Config::getInstance(self::$instance)->getClassInfo($classname);
        }
        catch (ConfigurationException $e)
        {
            return null;
        }

        if (class_exists($class_info['name'], false))
        {
            return $class_info['name'];
        }

        $class_path = ROOT_PATH . DIRECTORY_SEPARATOR . $class_info['path'];

        if (!file_exists($class_path))
        {
            throw SifoHttpException::InternalServerError("Doesn't exist in expected path {$class_info['path']}");
        }

        include_once($class_path);

        return $class_info['name'];
    }

    public static function dispatch(string $controller = null)
    {
        try
        {
            $domain      = Domains::getInstance();
            $destination = $domain->getRedirect();

            if (!empty($destination))
            {
                throw SifoHttpException::PermanentRedirect($destination);
            }

            $auth_data = $domain->getAuthData();

            if (!empty($auth_data) && FilterCookie::getInstance()->getString('domain_auth') != $auth_data['hash'])
            {
                $filter_server = FilterServer::getInstance();
                if ($filter_server->isEmpty('PHP_AUTH_USER') || $filter_server->isEmpty('PHP_AUTH_PW') || $filter_server->getString('PHP_AUTH_USER') != $auth_data['user']
                    || $filter_server->getString('PHP_AUTH_PW') != $auth_data['password']
                )
                {
                    Headers::set('WWW-Authenticate', 'Basic realm="Protected page"');
                    Headers::send();

                    throw SifoHttpException::NotAuthorized('You should enter a valid credentials.');
                }

                // If the user is authorized, we save a session cookie to prevent multiple auth under subdomains in the same session.
                setcookie('domain_auth', $auth_data['hash'], 0, '/', $domain->getDomain());
            }

            self::$language     = $domain->getLanguage();
            $additional_php_ini = $domain->getPhpInis();

            if ($additional_php_ini)
            {
                self::_overWritePHPini($additional_php_ini);
            }

            $url        = Urls::getInstance(self::$instance);
            $path_parts = $url->getPathParts();

            if (!$domain->valid_domain)
            {
                throw SifoHttpException::NotFound('Unknown language in domain');
            }

            if (null === $controller)
            {
                $router     = new Router($path_parts[0], self::$instance, $domain->getSubdomain(), self::$language, $domain->www_mode);
                $controller = $router->getController();
            }

            $ctrl             = self::invokeController($controller);
            self::$controller = $controller;

            $ctrl->addParams(['controller_route' => $controller]);

            self::manageFloatingDebugOptions();

            $ctrl->dispatch();

            if (false === $ctrl->is_json && Domains::getInstance()->getDebugMode())
            {
                self::invokeController('debug/index')->dispatch();
            }
        }
        catch (UnknownDomainException $d)
        {
            Headers::setResponseStatus(404);
            Headers::send();
            echo "<h1>{$d->getMessage()}</h1>";
            die;
        }
        catch (SifoHttpException $e)
        {
            if ($e->isRedirect())
            {
                self::dispatchRedirect($e);
            }
            else
            {
                self::dispatchErrorController($e);
            }
        }
        catch (ControllerException $e)
        {
            self::dispatchErrorController($e->getPrevious());
        }
        catch (\Exception $e)
        {
            $exception = SifoHttpException::InternalServerError($e->getMessage(), $e->getCode(), $e->getPrevious());
            self::dispatchErrorController($exception);
        }
    }

    private static function dispatchRedirect(SifoHttpException $exception)
    {
        $new_location = $exception->getRedirectLocation();

        if (Domains::getInstance()->getDebugMode())
        {
            $ctrl = self::invokeController('error/common');
            $ctrl->addParams(
                [
                    'code'         => $exception->getHttpCode(),
                    'code_msg'     => $exception->getHttpCodeMessage(),
                    'msg'          => $exception->getMessage(),
                    'trace'        => $exception->getTraceAsString(),
                    'url_redirect' => $new_location
                ]
            );
            $ctrl->dispatch();

            Headers::set('Location (paused)', $new_location);
            Headers::send();
            self::invokeController('debug/index')->dispatch();

            return;
        }

        Headers::setResponseStatus($exception->getHttpCode());
        Headers::set('Location', $new_location, $exception->getHttpCode());
        Headers::send();
    }

    private static function dispatchErrorController(SifoHttpException $exception)
    {
        Headers::setResponseStatus($exception->getHttpCode());
        Headers::send();

        $ctrl = self::invokeController('error/common');
        $ctrl->addParams(
            [
                'code'     => $exception->getHttpCode(),
                'code_msg' => $exception->getHttpCodeMessage(),
                'msg'      => $exception->getMessage(),
                'trace'    => $exception->getTraceAsString(),
            ]
        );

        $ctrl->dispatch();

        if (Domains::getInstance()->getDebugMode())
        {
            self::invokeController('debug/index')->dispatch();
        }
    }

    private static function _overWritePHPini(array $php_ini_values)
    {
        foreach ($php_ini_values as $key => $value)
        {
            ini_set($key, $value);
        }
    }

    private static function manageFloatingDebugOptions()
    {
        $domain = Domains::getInstance();

        if ($domain->getDevMode())
        {
            if (FilterGet::getInstance()->getInteger('clean_compile'))
            {
                $smarty_compiles_dir = ROOT_PATH . "/instances/" . self::$instance . "/templates/_smarty/compile/*";
                system('rm -rf ' . $smarty_compiles_dir);
            }

            if (FilterGet::getInstance()->getInteger('rebuild_all'))
            {
                Cookie::set('rebuild_all', 1);
            }
            if (FilterGet::getInstance()->getInteger('rebuild_nothing') && FilterCookie::getInstance()->getInteger('rebuild_all'))
            {
                Cookie::delete('rebuild_all');
            }
            if (1 === FilterGet::getInstance()->getInteger('debug'))
            {
                Cookie::set('debug', 1);
            }
            if (0 === FilterGet::getInstance()->getInteger('debug'))
            {
                Cookie::set('debug', 0);
            }

            if (false !== ($debug = FilterCookie::getInstance()->getInteger('debug')))
            {
                Domains::getInstance()->setDebugMode((bool) $debug);
            }
        }
    }
}
