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

use Sifo\Exception\SEO\Exception301;
use Sifo\Exception\SEO\Exception500;

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

	/**
	 * Get the used url to access. This info is important to resolve defined 301 redirections.
	 *
	 * @return string The used url.
	 */
	static public function getUsedUrl()
	{
		$server = \Sifo\Filter\Server::getInstance();

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
			throw new Exception500( "Failed opening router conifiguration file" );
		}

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
			catch ( ConfigurationException $e )
			{
				// trigger_error( "Failed to load url config profile for language '$language'" );
			}
		}

		foreach ( $routes as $route => $controller )
		{
			// The subdomain can define the controller to use.
			if ( $subdomain == $route )
			{
				$this->main_controller = $controller;
				return;
			}
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

						// $count indicates the replaces. If $count gt 0 means that was matchs.
						if ( $count )
						{
							throw new Exception301( $destiny );
						}
					}
				}
				// No route found, use default.
				$this->main_controller = $routes['__NO_ROUTE_FOUND__'];
			}
		}
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