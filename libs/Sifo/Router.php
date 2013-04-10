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
 * @author Albert Lombarte
 */
class Router
{

    static protected $reversal_map = array( );
    static protected $routes_for_this_language = array();
    protected $main_controller;
    static protected $route_vars = array();

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
     * @param $value
     * @param $expression
     * @return mixed
     * @throws \Sifo\Exception_404
     */
    protected function validParam($value,$expression) {

        if(function_exists($expression)){
            if($expression($value)) {
                return $value;
            }
        } elseif(preg_match($expression, $value)) {

            return $value;
        }
        else {
            throw new \Sifo\Exception_404();
        }
    }

    /**
     * Looks if the path has a known pattern handled by a controller.
     *
     * @param unknown_type $path
     */
    public function __construct( $path, $instance, $subdomain = false, $language = false, $www_mode = false )
    {
        // Look for routes:
        $routes = Config::getInstance( $instance )->getConfig( 'router' );

        // Failed to parse routes file.
        if ( !$routes )
        {
            throw new Exception_500( "Failed opening router conifiguration file" );
        }

        //TRANSLATIONS
        if ( $language )
        {
            try
            {
                self::$routes_for_this_language = Config::getInstance( $instance )->getConfig( 'lang/router_' . $language );


                // Translation of URLs:
                foreach ( self::$routes_for_this_language as $translated_route => $destiny )
                {
                    if ( isset( $routes[$translated_route] ) && $translated_route != $destiny )
                    {
                        // Replace a translation of the URL by the english entry.
                        $routes[$destiny] = $routes[$translated_route];

                        // Delete the English entry.
                        unset( $routes[$translated_route] );
                    }


                    // Create a mapping table with the association translated => original
                    self::$reversal_map[$destiny] = $translated_route;
                }
            }
            catch ( Exception_Configuration $e )
            {
                // trigger_error( "Failed to load url config profile for language '$language'" );
            }
        }

        //GET SUB DOMAIN
        if($subdomain==true){

            $url = $this->getUsedUrl();
            $parsedUrl = parse_url($url);
            $host = explode('.', $parsedUrl['host']);
            $subdomain = array_slice($host, 0, count($host) - 2 );
        }

        //FIND CONTROLLER
        foreach ( $routes as $route => $v )
        {

            //If is array, complex routing is used.
            if(is_array($v)){
                //Controller path
                $controller = $v['controller'];

                // Validate Controller REQUEST_METHOD. If no REQUEST_METHOD is set, keep going.
                if( isset($v['request_method']) ) {

                    if(!in_array($_SERVER['REQUEST_METHOD'],array_map("strtoupper",$v['request_method']))) {
                        throw new Exception_404();
                    }
                }

                // Validate placeholders and replace the placeholders for their real value
                if(preg_match_all('/{([^}]*)}/',$route,$match)){

                    $places = explode('/',$route);
                    $places_value = explode('/',$path);

                    // if we're using subdomains we must shift as many times as
                    // the total amout of subdomains levels to equal the 2 arrays size.
                    if($subdomain) {
                        $max=count($subdomain);
                        for($i=0;$i<$max;$i++) {
                            array_shift($places);
                        }
                    }

                    if(count($places)==count($places_value)){

                        $values_array = array_combine($places,$places_value);
                        foreach($match[1] as $regex_key => $regex_value){

                            $value = $values_array['{'.$regex_value.'}'];

                            if(isset($v['placeholders'][$regex_value])) {
                                $value=$this->validParam($value,$v['placeholders'][$regex_value]);
                            }
                            $route = str_replace('{'.$regex_value.'}',$value,$route);
                            self::$route_vars[]=$value;
                        }
                    }
                }
            } else {
                $controller = $v;
            }

            // CASE 1: PARAMS IN SUB DOMAIN
            // The subdomain can define the controller to use.
            if($subdomain)
            {
                if($path == str_replace(implode('/',$subdomain).'/','',$route)){
                    $this->main_controller = $controller;
                    return;
                }
            }
            // CASE 2: NO SUB DOMAIN
            // No valid subdomain for controller, use path to define controller instead:
            elseif ( $path == $route )
            {
                $this->main_controller = $controller;
                return;
            }
        }

        // The controller cannot be determined by parsing the path or the subdomain, is a home?
        if ( !isset( $this->main_controller ) )
        {
            if ( ( strlen( $path ) == 0 ) && !( ( $subdomain != "www" && true == $www_mode || strlen( $subdomain ) > 0 && false == $www_mode) ) )
            {
                $this->main_controller = $routes['__HOME__'];
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
                // No route found, use default.
                $this->main_controller = $routes['__NO_ROUTE_FOUND__'];
            }
        }
    }

    /**
     * @return array
     */
    static public function getControllerVars()
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