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
	static private $instance;
	static public $search_engine;

	private function __construct() {}

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
			$sphinx_active = Config::getInstance()->getConfig( 'sphinx', 'active' );

			// Check if Sphinx is enabled by configuration:
			if ( true === $sphinx_active )
			{
				include ROOT_PATH . '/libs/'.Config::getInstance()->getLibrary( 'sphinx' ) . '/sphinxapi.php';

				$sphinx_server 	= Config::getInstance()->getConfig( 'sphinx', 'server' );
				$sphinx_port 	= Config::getInstance()->getConfig( 'sphinx', 'port' );

				self::$search_engine 	= 'Sphinx';
				self::$instance 		= new \SphinxClient();
				self::$instance->SetServer( $sphinx_server, $sphinx_port );

				// Check that Sphinx is listening:
				if ( true ==! self::$instance->Open() )
				{
					trigger_error( 'Sphinx ('.$sphinx_server.':'.$sphinx_port.') is down!' );
					self::$instance = false;
				}
			}
		}

		return self::$instance;
	}

	/**
	 * Override parent RunQueries to put results into debug array and benchmark times.
	 *
	 * @return array
	 */
	function RunQueries()
	{
		Benchmark::getInstance()->timingStart( 'search' );

		$answer = self::$instance->RunQueries();

		$query_time = Benchmark::getInstance()->timingCurrentToRegistry( 'search' );

		Registry::push( 'searches', $answer );

		return $answer;
	}

	/**
	 * Delegate all calls to the proper class.
	 *
	 * @param string $method
	 * @param mixed $args
	 * @return mixed
	 */
	function __call($method, $args)//call adodb methods
	{
		return call_user_func_array(array(self::$instance, $method),$args);
	}
}