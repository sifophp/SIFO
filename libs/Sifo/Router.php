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

/**
 * Maps an URL with a controller
 *
 * @author Albert Lombarte, Nil Portugués Calderó
 */
class Router
{
    protected $main_controller;
    protected $router_regex = 'router_regex';
    protected $router_patterns = 'router';
    static protected $route_vars = array();
    static protected $routes_for_this_language = array();
    static protected $reversal_map = array( );


    /**
     * @param $path
     * @param $instance
     * @param bool $subdomain
     * @param bool $language
     * @param bool $www_mode
     */
    public function __construct($path,$instance,$subdomain = false,$language = false,$www_mode = false)
    {
        //load current instance routes.
        $config = Config::getInstance($instance);
        $router_patterns = $config->getConfig($this->router_patterns);	// router.config.php

        if ( empty($router_patterns) )
        {
            throw new Exception_500( "Failed opening router.config.php configuration file" );
        }

        $path_parts = explode('/',$path);

        //Check if we're being asked for homepage
        if(empty($path_parts[0]))
        {
            $this->main_controller = $router_patterns['__HOME__'];
            return $this;
        }

        //Subdomain can be defined as a controller. Build $path_parts as it wasn't a subdomain for further check.
        if($subdomain)
        {
            $subdomain = array_reverse($this->getSubdomain());
            do
            {
                array_unshift($path_parts,array_shift($subdomain));
            }while(!empty($subdomain));
        }
        $path = implode('/',$path_parts);

        //Check if we'll be using the ROUTE TRANSLATIONS instead of the loaded routes
        if($language)
        {
            $this->languageRouting($router_patterns,$instance,$language);
        }

        //The routing routine.
        $not_found = $router_patterns['__NO_ROUTE_FOUND__'];
        if(!empty($router_patterns[$path_parts[0]]))
        {
            //CASE 1: LEGACY COMPATIBILITY - Check if we're being asked for an existing controller
            if( !is_array($router_patterns[$path_parts[0]]))
            {
                $this->main_controller = $router_patterns[$path_parts[0]];
                return $this;
            }

            //CASE 2: Check if we're being asked for the controller with no subactions
            if(!empty($router_patterns[$path_parts[0]]['controller']) && substr_count($path, '/')==0)
            {
                $this->main_controller = $router_patterns[$path_parts[0]]['controller'];
                return $this;
            }

            //CASE 3: New router system implementation. Look for URL patterns and assign controller (and method if supplied).
            $router_patterns = $router_patterns[$path_parts[0]];
            if(!empty($router_patterns['pattern']))
            {
                $path = str_replace($path_parts[0].'/','',$path); //remove from the path the matched value.

                $router_patterns['pattern'] = str_replace('/','\/',$router_patterns['pattern']);

                //Replace all placeholders with regex values
                if(!empty($router_patterns['placeholders']))
                {
                    $this->placeholderSetRegExpressions($instance,$router_patterns);
                }

                //Search url
                $key = key($router_patterns['pattern']);
                $elem = array_shift($router_patterns['pattern']);
                do
                {
                    if( preg_match('/^'.$elem.'$/',$path,$vars))
                    {
                        //Check if execution will be allowed
                        $this->validRequestMethod($router_patterns['request_method'][$key]);

                        //Save values
                        unset($vars[0]);
                        self::$route_vars = $vars;

                        //Return controller+method
                        if(strpos($key,'::')===false)
                        {
                            $this->main_controller= $router_patterns['controller'];
                        }
                        else
                        {
                            $this->main_controller = $key;
                        }
                        return $this;
                    }

                    $key = key($router_patterns['pattern']);
                    $elem = array_shift($router_patterns['pattern']);

                } while(!empty($elem));

            }
        }

        //The controller cannot be determined by parsing the path or the subdomain
        if ( ( strlen( $path ) == 0 ) && !( ( $subdomain != "www" && true == $www_mode || strlen( $subdomain ) > 0 && false == $www_mode) ) )
        {
            $this->main_controller = $router_patterns['__HOME__'];
            return $this;
        }
        else
        {
            if ( $rules_301 = Config::getInstance( $instance )->getConfig( '301_rules' ) )
            {
                $used_url = self::getUsedUrl();
                foreach ( $rules_301 as $regexp => $replacement )
                {
                    $destiny = preg_replace( $regexp, $replacement, $used_url, -1, $count );

                    // $count indicates the replaces. If $count gt 0 means that matched.
                    if ( $count )
                    {
                        throw new Exception_301( $destiny );
                    }
                }
            }
        }
        $this->main_controller = $not_found;
        return $this;
    }

    /**
     * @param $routes
     * @param $instance
     * @param $language
     */
    protected function languageRouting(&$routes,$instance,$language)
    {
        try
        {
            self::$routes_for_this_language = Config::getInstance( $instance )->getConfig( 'lang/router_' . $language );

            //Really important or else URL with patterns won't be translated, as shorter translated URL could interfere.
            arsort(self::$routes_for_this_language);

            // Translation of URLs:
            foreach ( self::$routes_for_this_language as $original_route => $translated_route )
            {
                //Has  0 slashes, do this
                if(substr_count($original_route,'/')==0)
                {
                    if ( isset( $routes[$original_route] ) && $original_route != $translated_route )
                    {
                        // Replace a translation of the URL by the Original entry.
                        $routes[$translated_route] = $routes[$original_route];

                        // Delete the Original entry.
                        unset( $routes[$original_route] );
                    }
                }
                //Could have all pattern translated
                else
                {
                    $original = explode('/',$original_route);
                    if ( !empty($routes[$original[0]]['pattern']) && is_array($routes[$original[0]]['pattern']) )
                    {
                        //Get original pattern value
                        $pattern = explode('/',$original_route);
                        array_shift($pattern);
                        $pattern = implode('/',$pattern);

                        $key = array_search($pattern,$routes[$original[0]]['pattern']);
                        if($key)
                        {
                            //Get pattern translation
                            $translation = explode('/',$translated_route);
                            array_shift($translation);
                            $translation = implode('/',$translation);

                            // Replace a translation of the URL by the Original entry.
                            $routes[$original[0]]['pattern'][$key] = $translation;
                        }
                    }
                }
                // Create a mapping table with the association translated => original
                self::$reversal_map[$translated_route] = $translated_route;
            }
        }
        catch ( Exception_Configuration $e )
        {
            // trigger_error( "Failed to load url config profile for language '$language'" );
        }
    }


    /**
     * Returns an array with the subdomain values
     *
     * @return array
     */
    protected function getSubdomain()
    {
        $url = $this->getUsedUrl();
        $parsedUrl = parse_url($url);
        $host = explode('.', $parsedUrl['host']);
        return array_slice($host, 0, count($host) - 2 );
    }

    /**
     * Checks if a controller has specified being restricted to be requested by a subset of HTTP request methods.
     *
     * @param $config
     * @return bool
     * @throws Exception_404
     */
    protected function validRequestMethod(&$config)
    {
        // Validate Controller REQUEST_METHOD. If no REQUEST_METHOD is set, keep going.
        if( isset($config) )
        {
            if(!in_array( FilterServer::getInstance()->getString('REQUEST_METHOD'),array_map("strtoupper",$config)))
            {
                throw new Exception_404();
            }
        }
    }

    /**
     * @param $instance
     * @param $router_patterns
     * @throws Exception_500
     */
    protected function placeholderSetRegExpressions($instance,&$router_patterns)
    {
        $config = Config::getInstance($instance);
        $regex = $config->getConfig($this->router_regex);	// router_regex.config.php

        if ( empty($regex) )
        {
            throw new Exception_500( "Failed opening router_regex.config.php configuration file" );
        }

        //Replace placeholders for its regex equivalents
        $key = key($router_patterns['placeholders']);
        $elem = array_shift($router_patterns['placeholders']);
        do{
            //replace placeholder for regex
            $router_patterns['pattern'] = str_replace(array_values($elem),$regex[$key],$router_patterns['pattern']);

            //next item
            $key = key($router_patterns['placeholders']);
            $elem = array_shift($router_patterns['placeholders']);
        } while( !empty($elem) );

        //Replaces all unmatched {} keys with (.+)
        if(preg_match_all('/{([^}]*)}/',implode(',',$router_patterns['pattern']),$vars))
        {
            $router_patterns['pattern'] = str_replace($vars[0],'(.+)',$router_patterns['pattern']);
        }
    }


    /**
     * @return array
     */
    public function getControllerVars()
    {
        return self::$route_vars;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->main_controller;
    }


    /**
     * Get the used url to access. This info is important to resolve defined 301 redirections.
     *
     * @return string The used url.
     */
    static public function getUsedUrl()
    {
        $server = FilterServer::getInstance();

        $used_url = 'http';
        if ( $server->getString( "HTTPS" ) == "on" )
        {
            $used_url .= "s";
        }
        $used_url .= "://";

        if ( $server->getString( 'HTTP_HOST' ) )
        {
            $hostname = $server->getString( 'HTTP_HOST' );
        }
        else
        {
            $hostname = $server->getString( "SERVER_NAME" );
        }

        if ( $server->getString( 'SERVER_PORT' ) != "80" )
        {
            $used_url .= $hostname . ":" . $server->getString( 'SERVER_PORT' ) . $server->getString( "REQUEST_URI" );
        }
        else
        {
            $used_url .= $hostname . $server->getString( "REQUEST_URI" );
        }

        return $used_url;
    }

    /**
     * Returns the key associated to a translated route. Or same string if no reversal found.
     *
     * For instance, if you pass 'ayuda' should return 'help'.
     *
     * @param string $translated_route
     * @return string
     */
    static public function getReversalRoute( $translated_route )
    {
        if ( isset( self::$reversal_map[$translated_route] ) )
        {
            // The path has translation:
            return self::$reversal_map[$translated_route];
        }

        if ( !isset( self::$routes_for_this_language[ $translated_route ] )  )
        {
            // There are not available translation for this route.
            return $translated_route;
        }
        return false;
    }
}
