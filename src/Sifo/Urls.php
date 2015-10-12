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
 * Parses all the URLs.
 *
 * @author Albert Lombarte
 */
class Urls
{

	/**
	 * Singleton Instance.
	 *
	 * @var Urls
	 */
	static private $instance;
	/**
	 * Params passed via URL.
	 *
	 * @var string
	 */
	static private $params = false;
	/**
	 * Determines if values passed to URLs on Views are previously normalized.
	 *
	 * @var string
	 */
	static public $normalize_values = false;
	/**
	 * Evaluated scheme used (http|https).
	 *
	 * @var string
	 */
	static public $scheme = 'http';
	/**
	 * Evaluated path context.
	 *
	 * @var string
	 */
	static private $path = '';
	/**
	 * Splitted path.
	 *
	 * @var string
	 */
	static private $path_parts = array( );
	/**
	 * This is the address the user used to access this webpage. E.g: http://myapp.com
	 *
	 * Scope is public because is accessed directly from url.config while construct hasn't finished.
	 * @var string
	 */
	static public $base_url = '';
	/**
	 * This is the MAIN address of this webpage, without additional subdomains. E.g: http://myapp.com
	 *
	 * The only exceptions are those instances with www_as_subdomain in domains.config.
	 * @var string
	 */
	static public $main_url = '';
	/**
	 * This is the ACTUAL address of requested page, including protocol, complete
	 * hostname, path and additional parameters as own parameters & filters, querystring params, etc.
	 *
	 * @var string
	 */
	static public $actual_url = '';
	/**
	 * Stores the definition of an URL.
	 *
	 * @var array
	 */
	static public $url_definition = array( );
	/**
	 * Stores all the available URLs.
	 *
	 * @var array
	 */
	static private $url_config = array( );

	private $url_instance_config = array( );

	/**
	 * Singleton for managing URLs. Use this static method instead of construct.
	 *
	 * @return Urls
	 */
	static public function getInstance( $instance_name = null )
	{
		if ( null === $instance_name )
		{
			$instance_name = Bootstrap::$instance;
		}

		if ( !isset( self::$instance[$instance_name] ) )
		{
			self::$instance[$instance_name] = new Urls( $instance_name );
		}

		return self::$instance[$instance_name];
	}

	private function __construct( $instance_name )
	{
		$domains = Domains::getInstance( $instance_name );
		$filter_server = Filter\Server::getInstance();

		$language = $domains->getLanguage();

		$clean_host = preg_replace( '/^' . $domains->getSubdomain() . '\./', '', $domains->getDomain() );

		if ( $filter_server->getString( 'HTTPS' ) )
		{
			self::$scheme = 'https';
		}

		if ( true === $domains->www_mode )
		{
			self::$main_url = 'http://www.' . $clean_host;
		}
		else
		{
			self::$main_url = 'http://' . $clean_host;
		}

		// E.g: http://domain.com/folder1/subfolder1/subfolder11
		self::$base_url = self::$scheme . '://' . $filter_server->getString( 'HTTP_HOST' );
		self::$url_definition = Config::getInstance( $instance_name )->getConfig( 'url_definition' );

		$original_path = $filter_server->getString( 'REQUEST_URI' );
		$query_string = $filter_server->getString( 'QUERY_STRING' );
		$path = urldecode( str_replace( '?' . $query_string, '', $original_path ) );

		// E.g: http://subdomain.domain.com/folder1_param:x:value:y:value?utm_campaign=enloquecer_al_larry
		self::$actual_url = self::$base_url . $original_path;

		// If url ends in slash, remove it and redirect:
		if ( ( strlen( $path ) > 1 ) && ( substr( $path, -1 ) == '/' ) )
		{
			$path = trim($path, '/');
			if (!empty($query_string))
			{
				$path .= '?' . urldecode($query_string);
			}

			header( 'HTTP/1.0 Moved Permanently 301' );
			header( 'Location: ' . $this->getBaseUrl() . "/$path" );
			exit();
		}

		$path = ltrim( $path, '/' );
		// Separate parameters from URL:
		$params_parts = explode( self::$url_definition['params_separator'], $path );

		// Path is the first part of the explode, rest are parameters.
		self::$path = array_shift( $params_parts );

		if ( count( $params_parts ) > 0 )
		{
			self::$params = $params_parts;
		}
		else
		{
			self::$params = array();
		}

		self::$path_parts = explode( self::$url_definition['context_separator'], self::$path );

		// Default url.config for all languages.
		self::$url_config = $this->url_instance_config = Config::getInstance( $instance_name )->getConfig( 'url' );
	}

	/**
	 * Resets the path by setting the parts involved.
	 *
	 * @param array $parts
	 */
	public function resetPath( Array $parts )
	{
		self::$path_parts = $parts;
		self::$path = implode( self::$url_definition['context_separator'], self::$path_parts );
	}

	/**
	 * Returns the path. That is a URL without the given params.
	 *
	 * @return array
	 */
	public function getPath()
	{
		return self::$path;
	}

	/**
	 * Returns the path. That is a an array with the parts of the URL without the given params.
	 *
	 * @return array
	 */
	public function getPathParts()
	{
		return self::$path_parts;
	}

	/**
	 * Returns the parameters passed via URL.
	 *
	 * @return array
	 */
	public function getParams()
	{
		return self::$params;
	}

	/**
	 * Returns the http address used in this host.
	 *
	 * @return string
	 */
	public function getBaseUrl()
	{
		return self::$base_url;
	}

	/**
	 * Returns the MAIN http address used in this host.
	 *
	 * @return string
	 */
	public function getMainUrl()
	{
		return self::$main_url;
	}

	/**
	 * Returns the absolute URL for a given path.
	 *
	 * @param string $path Path, without starting or leading slashes, relative. E.g: 'my/path'
	 * @return string
	 */
	static public function getUrl( $url_config_key )
	{
		if ( isset( self::$url_config[$url_config_key] ) )
		{
			return self::$url_config[$url_config_key];
		}
		return false;
	}

	/**
	 * Returns the array of URLs known for the current language.
	 *
	 * @return array
	 */
	public function getUrlConfig()
	{
		return $this->url_instance_config;
	}

	/**
	 * Returns information that helps to determine how urls must be processed.
	 *
	 * @return array
	 */
	static public function getUrlDefinition()
	{
		return self::$url_definition;
	}

	/**
	 * Normalizes a string to be used in a URL context.
	 *
	 * Example: "Qué buena la paella!" to "que-buena-la-paella".
	 *
	 * @return string
	 */
	static public function normalize( $string )
	{
		$separator = self::$url_definition['word_separator'];

		// Replace most common caracters with his sanitized version.
		$string = strtr( utf8_decode( trim( preg_replace( '/\s+/', ' ', $string ) ) ),
						utf8_decode( "¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðòóôõöøùúûüýÿþŔŕñ' " ),
						utf8_decode( "YuAAAAAAACEEEEIIIIDOOOOOOUUUUYbsaaaaaaaceeeeiiiiooooooouuuuyybRrn" . $separator . $separator ) );

		// Remove characters other than a-z, 0-9 and the separator.
		$string = trim( preg_replace( '/[^0-9a-z' . $separator . ']/', '', strtolower( $string ) ), $separator );
		$string = preg_replace( '/' . $separator . '+/', $separator, $string );

		// And in case there is more bullshit..
		return utf8_encode( $string );
	}

	/**
	 * Builds an standard Sifo url.
	 *
	 * @param string $hostname The hostname of the url to be built. Must be a key in the url.config.
	 * @param string $controller The controller part of the url as defined in the router. Must be a key in the url.config.
	 * @param array $actions An array of parameters that will be available in the $params['path_parts'] array.
	 * @param array $params Url parameters that will be available in the $params['params'] array.
	 * @return string A sifo url.
	 */
	static public function buildUrl( $hostname, $controller, array $actions = array(), array $params = array() )
	{
		$url = Urls::getUrl( $hostname ) . '/';
		$callback = function( $a ) { return urlencode( $a ); };

		$actions = array_map($callback, $actions );
		array_unshift( $actions, Urls::getUrl( $controller ) );
		$url .= implode( self::$url_definition['context_separator'], $actions );

		if ( array() !== $params )
		{
			$url .= self::$url_definition['params_separator'];
		}
		$url .= implode( self::$url_definition['params_separator'], $params );

		return $url;
	}
}
