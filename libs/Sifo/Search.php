<?php
/**
 * LICENSE
 *
 * Copyright 2010 Albert Garcia
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

class Search
{
	static protected $instance;

	static public $search_engine;

	protected $sphinx;

	/**
	 * Initializes the class.
	 */
	protected function __construct()
	{
		$sphinx_active 	= Config::getInstance()->getConfig( 'sphinx', 'active' );

		// Check if Sphinx is enabled by configuration:
		if ( true === $sphinx_active )
		{
			include_once ROOT_PATH . '/libs/'.Config::getInstance()->getLibrary( 'sphinx' ) . '/sphinxapi.php';

			$sphinx_config = Config::getInstance()->getConfig( 'sphinx' );

			self::$search_engine 	= 'Sphinx';
			$this->sphinx 			= new \SphinxClient();
			$this->sphinx->SetServer( $sphinx_config['server'], $sphinx_config['port'] );

			// If it's defined a time out connection in config file:
			if( isset( $sphinx_config['time_out'] ) )
			{
				$this->sphinx->SetConnectTimeout( $sphinx_config['time_out'] );
			}

			// Check if Sphinx is listening:
			if ( true ==! $this->sphinx->Open() )
			{
				throw new \Sifo\Exception_500( 'Sphinx ('.$sphinx_config['server'].':'.$sphinx_config['port'].') is down!' );
			}
		}
		return $sphinx_config;
	}

	/**
	 * Singleton of config class.
	 *
	 * @param string $instance_name Instance Name, needed to determine correct paths.
	 * @return object Config
	 */
	public static function getInstance()
	{
		if ( !isset ( self::$instance ) )
		{
			if ( Domains::getInstance()->getDevMode() !== true )
			{
				self::$instance = new Search;
			}
			else
			{
				self::$instance = new DebugSearch;
			}
		}

		return self::$instance;
	}

	/**
	 * Delegate all calls to the proper class.
	 *
	 * @param string $method
	 * @param mixed $args
	 * @return mixed
	 */
	function __call( $method, $args )
	{
		if ( is_object( $this->sphinx ) )
		{
			return call_user_func_array( array( $this->sphinx, $method ), $args );
		}
		return null;
	}
}