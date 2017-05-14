<?php

namespace Sifo\Http;

use Sifo\Config;
use Sifo\Exception\ConfigurationException;
use Sifo\Exception\Http\InternalServerError;
use Sifo\Exception\Http\PermanentRedirect;
use Sifo\Http\Filter\FilterServer;

/**
 * Maps an URL with a controller
 *
 * @author Albert Lombarte
 */
class Router
{
    static protected $reversal_map = [];
    static protected $routes_for_this_language = [];
    protected $main_controller;

    public function __construct(
        string $path,
        string $instance,
        string $subdomain = null,
        string $language = null,
        bool $www_mode = false
    ) {
        // Look for routes:
        $routes = Config::getInstance($instance)->getConfig('router');

        // Failed to parse routes file.
        if (!$routes) {
            throw new InternalServerError("Failed opening router configuration file");
        }

        if ($language) {
            try {
                self::$routes_for_this_language = Config::getInstance($instance)->getConfig('lang/router_' . $language);

                // Translation of URLs:
                foreach (self::$routes_for_this_language as $translated_route => $destination) {
                    if (isset($routes[$translated_route]) && $translated_route != $destination) {
                        // Replace a translation of the URL by the english entry.
                        $routes[$destination] = $routes[$translated_route];

                        // Delete the English entry.
                        unset($routes[$translated_route]);
                    }

                    // Create a mapping table with the association translated => original
                    self::$reversal_map[$destination] = $translated_route;
                }
            } catch (ConfigurationException $e) {
                // trigger_error( "Failed to load url config profile for language '$language'" );
            }
        }

        foreach ($routes as $route => $controller) {
            // The subdomain can define the controller to use.
            if ($subdomain == $route) {
                $this->main_controller = $controller;

                return;
            } // No valid subdomain for controller, use path to define controller instead:
            elseif ($path == $route) {
                $this->main_controller = $controller;

                return;
            }
        }

        // The controller cannot be determined by parsing the path or the subdomain, is a home?
        if (!isset($this->main_controller)) {
            if ((strlen($path) == 0) && !(($subdomain != "www" && true == $www_mode || strlen($subdomain) > 0 && false == $www_mode))) {
                $this->main_controller = $routes['__HOME__'];
            } else {
                if ($rules_301 = Config::getInstance($instance)->getConfig('301_rules')) {
                    $used_url = self::getUsedUrl();
                    foreach ($rules_301 as $regexp => $replacement) {
                        $destination = preg_replace($regexp, $replacement, $used_url, -1, $count);

                        // $count indicates the replaces. If $count gt 0 means that was matchs.
                        if ($count) {
                            throw new PermanentRedirect($destination);
                        }
                    }
                }
                // No route found, use default.
                $this->main_controller = $routes['__NO_ROUTE_FOUND__'];
            }
        }
    }

    /**
     * Get the used url to access. This info is important to resolve defined 301 redirections.
     *
     * @return string The used url.
     */
    static private function getUsedUrl()
    {
        $server = FilterServer::getInstance();

        $used_url = 'http';
        if ($server->getString('HTTPS') == 'on' || $server->getString('HTTP_X_FORWARDED_PROTO') == 'https') {
            $used_url .= "s";
        }
        $used_url .= "://";

        if ($server->getString('HTTP_HOST')) {
            $hostname = $server->getString('HTTP_HOST');
        } else {
            $hostname = $server->getString("SERVER_NAME");
        }

        if ($server->getString('SERVER_PORT') != "80") {
            $used_url .= $hostname . ":" . $server->getString('SERVER_PORT') . $server->getString("REQUEST_URI");
        } else {
            $used_url .= $hostname . $server->getString("REQUEST_URI");
        }

        return $used_url;
    }

    public function getController()
    {
        return $this->main_controller;
    }

    /**
     * Returns the key associated to a translated route. Or same string if no reversal found.
     *
     * For instance, if you pass 'ayuda' should return 'help'.
     *
     * @param string $translated_route
     *
     * @return string
     */
    static public function getReversalRoute($translated_route)
    {
        if (isset(self::$reversal_map[$translated_route])) {
            // The path has translation:
            return self::$reversal_map[$translated_route];
        }

        if (!isset(self::$routes_for_this_language[$translated_route])) {
            // There are not available translation for this route.
            return $translated_route;
        }

        return false;
    }
}
