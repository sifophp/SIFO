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
	 * Redefines query method adding debug information.
	 * @param $query
	 * @param $tag
	 * @return ResultIterator
	 */
	public function query( $query, $tag = null )
	{
		Benchmark::getInstance()->timingStart( 'sphinxql' );

		$sphinx_results = parent::query( $query, $tag );

		$sphinx_time 	= Benchmark::getInstance()->timingCurrentToRegistry( 'sphinxql' );

		$query_info['query']            = $query;
		$query_info['resultset']        = ( $sphinx_results ) ? iterator_to_array( $sphinx_results ) : array();
		$query_info['returned_rows']    = ( $sphinx_results ) ? count( $query_info['resultset'] ) : 0;
		$this->query_debug['queries'][] = $query_info;

		$this->query_debug['controller']      = $this->getCallerClass();
		$this->query_debug['time']            = $sphinx_time;
		$this->query_debug['error']           = ( $sphinx_results ) ? '' : $this->sphinxql->error;;
		$this->query_debug['tag']             = $tag;
		$this->query_debug['connection_data'] = $this->sphinx_config;

		Debug::push( 'sphinxql', $this->query_debug );
		unset( $this->query_debug );

		return $sphinx_results;
	}

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

		$this->query_debug['controller']      = $this->getCallerClass();
		$this->query_debug['time']            = $sphinx_time;
		$this->query_debug['error']           = ( $this->sphinxql->errno ) ? $this->sphinxql->error : '';
		$this->query_debug['tag']             = $tag;
		$this->query_debug['connection_data'] = $this->sphinx_config;

		Debug::push( 'sphinxql', $this->query_debug );
		unset( $this->query_debug );
		unset( $this->queries );

		return $sphinx_results;
	}

	/**
	 * Redefines addQuery method adding debug information.
	 * @param $query
	 * @param null $tag
	 */
	public function addQuery( $query, $tag )
	{
		parent::addQuery( $query, $tag );
		$this->queries[] = array( 'query' => $query . ';', 'tag' => $tag );
	}

	/**
	 * Build the caller classes stack.
	 * @return string
	 */
	public function getCallerClass()
	{
		$trace = debug_backtrace();
		foreach( $trace as $steps )
		{
			$classes[$steps['class']] = $steps['class'];
		}

		return implode( ' > ', array_slice( $classes, 0, 4 ) );
	}

	/**
	 * Redefines logError class. Errors will be shown on debug.
	 * @param $error
	 */
	protected function logError( $error )
	{}
}