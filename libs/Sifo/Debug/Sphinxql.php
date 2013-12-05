<?php
/**
 * LICENSE
 *
 * Copyright 2013 Pablo Ros
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
 * SphinxQL Debug class.
 */
class DebugSphinxql extends Sphinxql
{
	/**
	 * @var array Query debug information.
	 */
	private $query_debug = array();

	/**
	 * @var array Queries to execute in multiQuery method.
	 */
	private $queries;

	/**
	 * Redefines multiQuery method adding debug information.
	 * @param null $tag
	 * @return array|bool
	 */
	public function multiQuery( $tag = null )
	{
		Benchmark::getInstance()->timingStart( 'sphinxql' );
		$sphinx_results = parent::multiQuery( $tag );
		$sphinx_time 	= Benchmark::getInstance()->timingCurrentToRegistry( 'sphinxql' );

		foreach( $this->queries  as $key => $query )
		{
			$query_info['query']            = $query['query'];
			$query_info['tag']            	= $query['tag'];
			$query_info['resultset']        = ( !empty( $sphinx_results[$key] ) ) ? $sphinx_results[$key] : array();
			$query_info['returned_rows']    = ( !empty( $sphinx_results[$key] ) ) ? count( $query_info['resultset'] ) : 0;
			$this->query_debug['queries'][] = $query_info;
		}

		$this->query_debug['backtrace']      = $this->getCallerClass();
		$this->query_debug['time']            = $sphinx_time;
		$this->query_debug['error']           = ( $this->sphinxql->errno ) ? $this->sphinxql->error : '';
		$this->query_debug['tag']             = $tag;
		$this->query_debug['connection_data'] = $this->sphinx_config;

		Debug::push( 'sphinxql', $this->query_debug );

		if ( $this->sphinxql->errno )
		{
			Debug::push( 'sphinxql_errors', $this->sphinxql->error );
		}

		unset( $this->query_debug );
		unset( $this->queries );

		return $sphinx_results;
	}

	/**
	 * Redefines addQuery method adding debug information.
	 * @param $query
	 * @param null $tag
	 * @param array $parameters
	 *
	 * @return string The query after being prepared.
	 */
	public function addQuery( $query, $tag = null, $parameters = array() )
	{
		$prepared_query = parent::addQuery( $query, $tag, $parameters );
		$this->queries[] = array( 'query' => $prepared_query . ';', 'tag' => $tag );

		return $prepared_query;
	}

	/**
	 * Build the caller classes stack.
	 * @return string
	 */
	public function getCallerClass()
	{
		$array_debug = debug_backtrace();

		$trace = array();
		$step = 0;
		foreach ( array_reverse( $array_debug ) as $debug_step )
		{
			if ( !isset( $debug_step['class'] ) )
			{
				$debug_step['class'] = '';
			}
			if ( !isset( $debug_step['function'] ) )
			{
				$debug_step['function'] = '';
			}
			if ( !isset( $debug_step['file'] ) )
			{
				$debug_step['file'] = '';
			}
			if ( !isset( $debug_step['line'] ) )
			{
				$debug_step['line'] = '';
			}

			++$step;
			$trace[] = "$step > ".$debug_step['class'].'::'.$debug_step['function']
				.' - '.basename ( $debug_step['file'] )
				.':'.$debug_step['line']." [".dirname( $debug_step['file'] )."]";

			if ( in_array( $debug_step['function'], array( 'query', 'multiQuery' ) ) )
			{
				break;
			}
		}

		return $trace;
	}

	/**
	 * Redefines logError class. Errors will be shown on debug.
	 * @param $error
	 */
	protected function logError( $error )
	{}
}