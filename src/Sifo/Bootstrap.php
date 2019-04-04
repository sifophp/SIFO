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
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Sifo;

$is_defined_in_vhost = (false !== ini_get('newrelic.appname') && 'PHP Application' !== ini_get('newrelic.appname'));
if (!$is_defined_in_vhost && extension_loaded('newrelic') && isset($instance)) {
    newrelic_set_appname(ucfirst($instance));
}

/**
 * Class Bootstrap
 */
require_once ROOT_PATH . '/vendor/sifophp/sifo/src/Sifo/Exceptions.php';
require_once ROOT_PATH . '/vendor/sifophp/sifo/src/Sifo/Config.php';
require_once ROOT_PATH . '/vendor/autoload.php';

class Bootstrap
{
    /**
     * Root path.
     *
     * @var string
     */
    public static $root;
    /**
     * Application path.
     *
     * @var string
     */
    public static $application;
    /**
     * Instance name, this is the folder under 'instances'.
     *
     * @var string
     */
    public static $instance;
    /**
     * Language of this instance.
     *
     * @var string
     */
    public static $language;
    /**
     * The executed controller.
     *
     * @var string
     */
    public static $controller = null;
    /**
     * The dependency injection container.
     *
     * @var DependencyInjector
     */
    public static $container;

    /**
     * Starts the execution. Root path is passed to avoid recalculation.
     *
     * @param $instance_name
     * @param string $controller_name Optional, a controller to execute. If null the router will be used to determine it.
     *
     * @internal param string $root Path to root.
     *
     */
    public static function execute(
        $instance_name,
        $controller_name = null
    ) {
        // Set paths:
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
     */
    public static function dispatch($controller = null)
    {
        try {
            $domain = Domains::getInstance();
            $destination = $domain->getRedirect();

            if (!empty($destination)) {
                Headers::setResponseStatus(301);
                Headers::set('Location', $destination, 301);
                Headers::send();
                exit;
            }

            $auth_data = $domain->getAuthData();

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
                setcookie('domain_auth', $auth_data['hash'], 0, '/', $domain->getDomain());
            }

            self::$language = $domain->getLanguage();
            $php_inis = $domain->getPHPInis();

            if ($php_inis) {
                self::_overWritePHPini($php_inis);
            }

            $url = Urls::getInstance(self::$instance);
            $path_parts = $url->getPathParts();

            if (!$domain->valid_domain) {
                throw new Exception_404('Unknown language in domain');
            } else {
                if (null === $controller) {
                    $router = new Router($path_parts[0], self::$instance, $domain->getSubdomain(), self::$language, $domain->www_mode);
                    $controller = $router->getController();
                }
            }

            // This is the controller to use:
            $ctrl = self::invokeController($controller);
            self::$controller = $controller;

            // Save in params for future references:
            $ctrl->addParams(
                [
                    'controller_route' => $controller,
                ]
            );

            // Active/deactive auto-rebuild option:
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
     * @param Exception $e
     *
     * @return output buffer
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
            $new_location = '';
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

        $result = $ctrl2->dispatch();
        // Load the debug in case you have enabled the has debug flag.
        if (Domains::getInstance()->getDebugMode()) {
            self::invokeController('debug/index')->dispatch();
        }

        return $result;
    }

    /**
     * Sets all the PHP ini configurations stored in the configuration.
     *
     * @param array $php_inis
     */
    private static function _overWritePHPini(Array $php_inis)
    {
        foreach ($php_inis as $varname => $newvalue) {
            ini_set($varname, $newvalue);
        }
    }
}
