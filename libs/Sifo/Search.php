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

include ROOT_PATH . '/libs/'.Config::getInstance()->getLibrary( 'sphinx' ) . '/sphinxapi.php';

class Search extends \SphinxClient
{
	static private $instance;
	static public $search_engine;

	/**
	 * Initializes the class.
	 */
	private function __construct()
	{
		$this->SphinxClient();

		$sphinx_active = Config::getInstance()->getConfig( 'sphinx', 'active' );

		// Check if Sphinx is enabled by configuration:
		if ( true === $sphinx_active )
		{
			$sphinx_server 	= Config::getInstance()->getConfig( 'sphinx', 'server' );
			$sphinx_port 	= Config::getInstance()->getConfig( 'sphinx', 'port' );

			self::$search_engine 	= 'Sphinx';
			$this->SetServer( $sphinx_server, $sphinx_port );

			// Check that Sphinx is listening:
			if ( true ==! $this->Open() )
			{
				trigger_error( 'Sphinx ('.$sphinx_server.':'.$sphinx_port.') is down!' );
			}
		}
	}

	/**
	 * Singleton of search class.
	 *
	 * @return object Search
	 */
	public static function getInstance()
	{
		if ( !isset ( self::$instance ) )
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Override parent RunQueries to put results into debug array and benchmark times.
	 *
	 * @return array
	 */
	public function RunQueries( $search_tag = null )
	{
		if ( Bootstrap::$debug )
		{
			Benchmark::getInstance()->timingStart( 'search' );
			$answer = parent::RunQueries();
			Benchmark::getInstance()->timingCurrentToRegistry( 'search' );

			Registry::push( 'searches', $answer );
		}
		else
		{
			$answer = parent::RunQueries();
		}

		return $answer;
	}
}