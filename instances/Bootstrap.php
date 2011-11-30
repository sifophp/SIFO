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
 * Class Bootstrap
 */
require_once ROOT_PATH . '/libs/Sifo/Config.php';

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
	 * If debug is active or not.
	 *
	 * @var boolean
	 */
	public static $debug = false;

	/**
	 * This classes will be loaded in this order and ALWAYS before starting
	 * to parse code. This array can be replaced in your libraries.config under
	 * the key $config['classes_always_preloaded']
	 *
	 * @var array
	 */
	public static $required_classes = array(
		'Exceptions',
		'Filter',
		'Domains',
		'Urls',
		'Router',
		'Controller'
	);

	/**
	 * Include the minimum necessary files to run SIFO.
	 */
	public static function includeRequiredFiles()
	{
		foreach ( self::$required_classes as $class )
		{
			self::includeFile( $class );
		}
	}

	/**
	 * Starts the execution. Root path is passed to avoid recalculation.
	 *
	 * @param $instance_name
	 * @param string $controller_name Optional, a controller to execute. If null the router will be used to determine it.
	 *
	 * @internal param string $root Path to root.
	 *
	 */
	public static function execute( $instance_name, $controller_name = null )
	{
		// Set paths:
		self::$root = ROOT_PATH;
		self::$application = dirname( __FILE__ );
		self::$instance = $instance_name;

		// Include files:
		self::includeRequiredFiles();

		// Register autoloader:
		spl_autoload_register( array( '\\Sifo\Bootstrap', 'includeFile' ) );

		Benchmark::getInstance()->timingStart();

		self::dispatch( $controller_name );

		Benchmark::getInstance()->timingStop();
	}

    /**
     * Invokes a controller with the folder/action form.
     *
     * @param string $controller The controller in folder/action form.
     * @return Controller|void
     */
	public static function invokeController( $controller )
	{
		$controller_path = explode( '/', $controller );

		$class = '';
		foreach ( $controller_path as $part )
		{
			$class .= ucfirst( $part );
		}

		$class .= 'Controller';

		return self::getClass( $class );
	}

	/**
	 * Includes (include_once) the file corresponding to the passed passed classname.
	 * It does not instantiate any object.
	 *
	 * This method must be public as it is used in external places, as unit-tests.
	 *
	 * @param string $classname
	 * @return string The classname you asked for.
	 */
	public static function includeFile( $classname )
	{
		try
		{
			$classInfo = Config::getInstance( self::$instance )->getClassInfo( $classname );
		}
		catch ( Exception_Configuration $e )
		{
			throw new Exception_500( $e->getMessage() );
		}

		if ( !include_once ROOT_PATH . '/' . $classInfo['path'] )
		{
			throw new Exception_500( "Doesn't exist in expected path {$classInfo['path']}" );
		}

		return $classInfo['name'];
	}

	/**
	 * Returns an instance of the requested class. The second parameter controls
	 * if an instance of the object is returned. If you are getting a class with
	 * a private constructor (e.g: singleton) set it to false.
	 *
	 * @param string $class Class name you want to get
	 * @param boolean $call_constructor Return a new object of the class (true), or include the class only (false).
	 * @return Object|void
	 */
	public static function getClass( $class, $call_constructor = true )
	{
		$classname = self::includeFile( $class );

		if ( class_exists( $classname ) )
		{
			if ( $call_constructor )
			{
				return new $classname;
			}
		}
		else
		{
			throw new Exception_500( "Method getClass($class) failed because the class $classname is not declared inside this file (a copy/paste friend?)." );
		}
	}

	/**
	 * Sets the controller and view properties and executes the controller, sending the output buffer.
	 *
	 * @param string $controller Dispatches a specific controller, or use URL to determine the controller
	 */
	public static function dispatch( $controller = null )
	{
		try
		{
			$config = Config::getInstance( self::$instance );
			$domain = Domains::getInstance();
			$filter_server = FilterServer::getInstance();
			$filter_cookie = FilterCookie::getInstance();

			$destiny = $domain->getRedirect();
			if ( !empty( $destiny ) )
			{
				header( 'HTTP/1.0 301 Moved Permanently' );
				header( "Location: " . $destiny, true, 301 );
				exit;
			}

			$auth_data = $domain->getAuthData();
			$ip_user = $filter_server->getString( 'REMOTE_ADDR' );

			// [TODO FIX ERROR] Disabled "trusted_ips" filter due to regular auth system not working.
			// if ( $filter_cookie->isEmpty( 'seofwauth' ) && !empty( $auth_data ) && isset( $auth_data['trusted_ips'] ) && !in_array( $ip_user, $auth_data['trusted_ips'] ) )

			if ( $filter_cookie->isEmpty( 'seofwauth' ) && !empty( $auth_data ) )
			{
				if ( $filter_server->isEmpty( 'PHP_AUTH_USER' ) || $filter_server->isEmpty( 'PHP_AUTH_PW' ) || $filter_server->getString( 'PHP_AUTH_USER' ) != $auth_data['user'] || $filter_server->getString( 'PHP_AUTH_PW' ) != $auth_data['password'] )
				{
					header( 'WWW-Authenticate: Basic realm="Protected page"' );
					throw new Exception_401( 'You should enter a valid credentials.' );
				}
				// If the user is authorized, we save a session cookie to prevent multiple auth under subdomains in the same session.
				setcookie( 'seofwauth', 'true', 0, '/', $domain->getDomain() );
			}

			self::$language = $domain->getLanguage();
			$php_inis = $domain->getPHPInis();

			if ( $php_inis )
			{
				self::_overWritePHPini( $php_inis );
			}

			$url = Urls::getInstance( self::$instance );
			$path_parts = $url->getPathParts();

			if ( !$domain->valid_domain )
			{
				throw new Exception_404( 'Unknown language in domain' );
			}
			else
			{
				if ( null === $controller )
				{
					$router = new Router( $path_parts[0], self::$instance, $domain->getSubdomain(), self::$language, $domain->www_mode );
					$controller = $router->getController();
				}
			}

			// This is the controller to use:
			$ctrl = self::invokeController( $controller );

			// Save in params for future references:
			$ctrl->addParams( array(
				'controller_route' => $controller,
			) );

			// Active/deactive auto-rebuild option:
			if ( $domain->getDevMode() )
			{
				$ctrl->getClass( 'Cookie' );
				if ( FilterGet::getInstance()->getInteger( 'rebuild_all' ) )
				{
					Cookie::set( 'rebuild_all', 1 );
				}
				if ( FilterGet::getInstance()->getInteger( 'rebuild_nothing' ) && FilterCookie::getInstance()->getInteger( 'rebuild_all' ) )
				{
					Cookie::delete( 'rebuild_all' );
				}
				if ( 1 === FilterGet::getInstance()->getInteger( 'debug' ) )
				{
					Cookie::set( 'debug', 1 );
				}
				if ( 0 === FilterGet::getInstance()->getInteger( 'debug' ) )
				{
					Cookie::delete( 'debug' );
				}

                if ( FilterCookie::getInstance()->getInteger( 'debug' ) || FilterGet::getInstance()->getInteger( 'debug' ) )
                {
                    self::$debug = true;
                }
			}

			$ctrl->dispatch();

			if ( false === $ctrl->is_json && $ctrl->hasDebug() )
			{
				self::invokeController( 'debug/index' )->dispatch();
			}
		}
		catch ( \Exception $e )
		{
			self::_dispatchErrorController( $e );
		}
	}

	/**
	 * Dispatches an error after an exception.
	 *
	 * @param Exception $e
	 * @return output buffer
	 */
	private static function _dispatchErrorController( $e )
	{
		if ( !isset( $e->http_code ) )
		{
			$e->http_code = 503;
			$e->http_code_msg = 'Exception!';
			$e->redirect = false;
		}

		header( 'HTTP/1.0 ' . $e->http_code . ' ' . $e->http_code_msg );

		// Execute ErrorCommonController when an exception is captured.
		$ctrl2 = self::invokeController( 'error/common' );

		// Set params:
		$ctrl2->addParams( array(
			'code' => $e->http_code,
			'code_msg' => $e->http_code_msg,
			'msg' => $e->getMessage(),
			'trace' => $e->getTraceAsString(),
		) );

		// All the SEO_Exceptions with need of redirection have this attribute:
		if ( $e->redirect )
		{
			// Path is passed via message:
			$path = trim( $e->getMessage(), '/' );
			$new_location = '';
			// Check if the URL for the redirection has already a protocol, like http:// , https://, ftp://, etc..
			if ( false !== strpos( $path, '://' ) )
			{
				// Absolute path passed:
				$new_location = $path;
			}
			else
			{
				// Relative path passed, use path as the key in url.config.php file:
				$new_location = Urls::getUrl( $path );
			}

			if ( empty( $new_location ) || false == $new_location )
			{
				trigger_error( "Exception " . $e->http_code . " raised with an empty location " . $e->getTraceAsString() );
				header( 'HTTP/1.0 500 Internal Server Error' );
				exit;
			}

			if ( !$ctrl2->hasDebug() )
			{
				header( "Location: " . $new_location, true, $e->http_code );
			}
			else
			{
				$ctrl2->addParams( array( 'url_redirect' => $new_location ) );
				$ctrl2->dispatch();
				self::invokeController( 'debug/index' )->dispatch();
				return;
			}
		}

		$result = $ctrl2->dispatch();
		// Load the debug in case you have enabled the devel flag.
		if ( $ctrl2->hasDebug() )
		{
			self::invokeController( 'debug/index' )->dispatch();
		}

		return $result;
	}

	/**
	 * Sets all the PHP ini configurations stored in the configuration.
	 *
	 * @param array $php_inis
	 */
	private static function _overWritePHPini( Array $php_inis )
	{
		foreach ( $php_inis as $varname => $newvalue )
		{
			ini_set( $varname, $newvalue );
		}
	}
}