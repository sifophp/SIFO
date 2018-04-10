<?php

namespace Sifo;

use Psr\Container\ContainerInterface;
use Sifo\Container\DependencyInjector;
use Sifo\Controller\Controller;
use Sifo\Controller\Debug\DebugController;
use Sifo\Controller\Error\ErrorController;
use Sifo\Exception\ControllerException;
use Sifo\Exception\Http\BaseException;
use Sifo\Exception\Http\InternalServerError;
use Sifo\Exception\Http\Redirect;
use Sifo\Exception\Http\Unauthorized;
use Sifo\Exception\UnknownDomainException;
use Sifo\Http\Cookie;
use Sifo\Http\Domains;
use Sifo\Http\Filter\FilterCookie;
use Sifo\Http\Filter\FilterGet;
use Sifo\Http\Filter\FilterServer;
use Sifo\Http\Headers;
use Sifo\Http\Router;
use Sifo\Http\Urls;
use Symfony\Component\HttpFoundation\Request;

require_once ROOT_PATH . '/vendor/autoload.php';

class Bootstrap
{
    /** @var Request */
    public static $request;

    /** @var string */
    public static $instance;

    /** @var string */
    public static $language;

    /** @var string */
    public static $controller;

    /** @var ContainerInterface */
    public static $container;

    /** @var Domains */
    protected static $domain;

    public function __construct()
    {
    }

    public static function execute(Request $request = null)
    {
        Benchmark::getInstance()->timingStart();

        self::$request = $request ?: self::getCurrentRequest();
        self::$domain = Domains::getInstance();
        self::$instance = self::$domain->getInstanceName();
        self::$container = DependencyInjector::getInstance(self::$instance);
        static::dispatch();

        Benchmark::getInstance()->timingStop();
    }

    public static function getCurrentRequest(): Request
    {
        return self::$request ?: Request::createFromGlobals();
    }

    public static function invokeController(string $controller_class): Controller
    {
        if (!class_exists($controller_class)) {
            /** @deprecated You should use the full qualified name when calling controllers. */
            $simple_controller_class = array_reduce(explode('/', $controller_class), function ($previous, $current) {
                    $previous .= ucfirst($current);
                    return $previous;
                }, '') . 'Controller';
            $instance_inheritance = array_reverse(Domains::getInstance()->getInstanceInheritance());
            foreach ($instance_inheritance as $instance) {
                $controller_class = ucfirst($instance) . '\\' . $simple_controller_class;
                if (class_exists($controller_class)) {
                    break;
                }
            }
        }

        /** @var Controller $controller */
        $controller = new $controller_class();
        $controller->setContainer(self::$container);

        return $controller;
    }

    public static function dispatch(string $controller = null)
    {
        try {
            $domain = Domains::getInstance();
            $auth_data = $domain->getAuthData();

            if (!empty($auth_data) && FilterCookie::getInstance()->getString('domain_auth') !== $auth_data['hash']) {
                $filter_server = FilterServer::getInstance();
                if ($filter_server->isEmpty('PHP_AUTH_USER')
                    || $filter_server->isEmpty('PHP_AUTH_PW')
                    || $filter_server->getString('PHP_AUTH_USER') !== $auth_data['user']
                    || $filter_server->getString('PHP_AUTH_PW') !== $auth_data['password']
                ) {
                    Headers::set('WWW-Authenticate', 'Basic realm="Protected page"');
                    Headers::send();

                    throw new Unauthorized('You should enter a valid credentials.');
                }

                // If the user is authorized, we save a session cookie to prevent multiple auth under subdomains in the same session.
                setcookie('domain_auth', $auth_data['hash'], 0, '/', $domain->getDomain());
            }

            self::$language = $domain->getLanguage();
            $additional_php_ini = $domain->getPhpInis();

            if ($additional_php_ini) {
                self::_overWritePHPini($additional_php_ini);
            }

            $url = Urls::getInstance(self::$instance);
            $path_parts = $url->getPathParts();

            if (null === $controller) {
                $router = new Router(
                    $path_parts[0],
                    self::$instance,
                    $domain->getSubdomain(),
                    self::$language,
                    $domain->www_mode
                );
                $controller = $router->getController();
            }

            $ctrl = self::invokeController($controller);
            self::$controller = $controller;

            $ctrl->addParams(['controller_route' => $controller]);

            self::manageFloatingDebugOptions();

            $ctrl->dispatch();

            if (false === $ctrl->is_json && Domains::getInstance()->getDebugMode()) {
                self::invokeController(DebugController::class)->dispatch();
            }
        } catch (UnknownDomainException $d) {
            Headers::setResponseStatus(404);
            Headers::send();
            echo "<h1>{$d->getMessage()}</h1>";
            die;
        } catch (Redirect $e) {
            self::dispatchRedirect($e);
        } catch (BaseException $e) {
            self::dispatchErrorController($e);
        } catch (ControllerException $e) {
            self::dispatchErrorController($e->getPrevious());
        } catch (\Exception $e) {
            self::dispatchErrorController(new InternalServerError($e->getMessage(), $e->getCode(), $e->getPrevious()));
        }
    }

    private static function dispatchRedirect(Redirect $exception)
    {
        $new_location = $exception->getRedirectLocation();

        if (Domains::getInstance()->getDebugMode()) {
            $ctrl = self::invokeController(ErrorController::class);
            $ctrl->addParams(
                [
                    'code' => $exception->getHttpCode(),
                    'code_msg' => $exception->getHttpCodeMessage(),
                    'msg' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                    'url_redirect' => $new_location
                ]
            );

            $ctrl->dispatch();

            self::invokeController(DebugController::class)->dispatch();

            return;
        }

        Headers::setResponseStatus($exception->getHttpCode());
        Headers::set('Location', $new_location, $exception->getHttpCode());
        Headers::send();
    }

    private static function dispatchErrorController(BaseException $exception)
    {
        if ($exception instanceof Redirect) {
            self::dispatchRedirect($exception);
            return;
        }

        Headers::setResponseStatus($exception->getHttpCode());
        Headers::send();

        $ctrl = self::invokeController('error/common');
        $ctrl->addParams(
            [
                'code' => $exception->getHttpCode(),
                'code_msg' => $exception->getHttpCodeMessage(),
                'msg' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]
        );

        $ctrl->dispatch();

        if (Domains::getInstance()->getDebugMode()) {
            self::invokeController(DebugController::class)->dispatch();
        }
    }

    private static function _overWritePHPini(array $php_ini_values)
    {
        foreach ($php_ini_values as $key => $value) {
            ini_set($key, $value);
        }
    }

    private static function manageFloatingDebugOptions()
    {
        $domain = Domains::getInstance();

        if ($domain->getDevMode()) {
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
