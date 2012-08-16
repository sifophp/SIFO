<?php
/**
 * LICENSE
 *
 * Copyright 2011 Pablo Ros
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
 * Search extension using debug features. It will be used ONLY on devel mode or with debug enable.
 */
class DebugSearch extends Search
{
	/**
	 * @var Store all the information related to queries to show it at the end in the debug.
	 */
	private $query_debug;

	/**
	 * @var Store the query context. It means: filters, order, group by, etc.
	 */
	private $query_context = array();

	/**
	 * @var array Text related to sort functions. Needed to show it in the debug.
	 */
	private $sort_text = array(
		0 => "SPH_SORT_RELEVANCE",
		1 => "SPH_SORT_ATTR_DESC",
		2 => "SPH_SORT_ATTR_ASC",
		3 => "SPH_SORT_TIME_SEGMENTS",
		4 => "SPH_SORT_EXTENDED",
		5 => "SPH_SORT_EXPR"
	);

	/**
	 * @var array Text related to group by functions. Needed to show it in the debug.
	 */
	private $group_text = array(
		0 => "SPH_GROUPBY_DAY",
		1 => "SPH_GROUPBY_WEEK",
		2 => "SPH_GROUPBY_MONTH",
		3 => "SPH_GROUPBY_YEAR",
		4 => "SPH_GROUPBY_ATTR",
		5 => "SPH_GROUPBY_ATTRPAIR"
	);

	/**
	 * Rewrite construct to init some debug parameters.
	 */
	protected function __construct()
	{
		$sphinx_config = parent::__construct();

		// Init Debug:
		$this->query_debug['current_query'] = 0;
		$this->query_debug = array_merge( $this->query_debug, $sphinx_config);
	}

	/**
	 * Rewrite SetSortMode to init some debug parameters.
	 * @param $mode
	 * @param string $sortby
	 * @return void
	 */
	public function SetSortMode( $mode, $sortby = "" )
	{
		$this->sphinx->SetSortMode( $mode, $sortby );

		$this->query_context['sort']['mode'] 		= $this->sort_text[$mode];
		$this->query_context['sort']['sortby']		= $sortby;
	}

	/**
	 * Rewrite SetGroupBy to init some debug parameters.
	 * @param $attribute
	 * @param $func
	 * @param string $groupsort
	 * @return void
	 */
	public function SetGroupBy( $attribute, $func, $groupsort = "@group desc" )
	{
		$this->sphinx->SetGroupBy( $attribute, $func, $groupsort );

		$this->query_context['group']['attribute'] 	= $attribute;
		$this->query_context['group']['func'] 		= $this->group_text[$func];
		$this->query_context['group']['groupsort'] 	= $groupsort;
	}

	/**
	 * Rewrite SetFilter to init some debug parameters.
	 * @param $attribute
	 * @param $values
	 * @param bool $exclude
	 * @return void
	 */
	public function SetFilter( $attribute, $values, $exclude = false )
	{
		$this->sphinx->SetFilter( $attribute, $values, $exclude );

		$filter_debug['attribute'] = $attribute;
		$filter_debug['values'] 	= $values;
		$filter_debug['exclude'] 	= $exclude;

		$this->query_context['filters'][] = $filter_debug;
	}

	/**
	 * Rewrite SetFilterRange to init some debug parameters.
	 * @param $attribute
	 * @param $min
	 * @param $max
	 * @param bool $exclude
	 * @return void
	 */
	public function SetFilterRange( $attribute, $min, $max, $exclude = false )
	{
		$this->sphinx->SetFilterRange( $attribute, $min, $max, $exclude );

		$filter_debug['attribute'] 	= $attribute;
		$filter_debug['values'] 	= $min . '-' . $max;
		$filter_debug['exclude'] 	= $exclude;

		$this->query_context['filters'][] = $filter_debug;
	}

	/**
	 * Delete filters in debug information.
	 */
	public function ResetFilters()
	{
		unset( $this->query_context['filters'] );
		$this->sphinx->ResetFilters();
	}

	/**
	 * Delete group by in debug information.
	 */
	public function ResetGroupBy()
	{
		unset( $this->query_context['group'] );
		$this->sphinx->ResetGroupBy();
	}

	/**
	 * Rewrite RunQueries to init some debug parameters.
	 * @param string $tag
	 * @return array|bool
	 */
	public function RunQueries( $tag = '' )
	{
		Benchmark::getInstance()->timingStart( 'search' );
		$sphinx_results = $this->sphinx->RunQueries();
		$sphinx_time 	= Benchmark::getInstance()->timingCurrentToRegistry( 'search' );

		if ( is_array( $sphinx_results ) )
		{
			foreach( $sphinx_results as $key => $result )
			{
				$this->query_debug['queries'][$key]['resultset'] 	= $result;
				$this->query_debug['queries'][$key]['total_found'] 	= ( isset( $result['total_found'] ) ) ? $result['total_found'] : 0;
				$this->query_debug['queries'][$key]['returned_rows'] = ( isset( $result['matches'] ) ) ? count( $result['matches'] ) : 0;
				$this->query_debug['queries'][$key]['error'] 		= ( isset( $result['error'] ) ) ? $result['error'] : '';
			}
		}

		$this->query_debug['controller'] 	= $this->getCallerClass();
		$this->query_debug['time'] 			= $sphinx_time;
		$this->query_debug['error'] 		= $this->sphinx->_error;
		$this->query_debug['tag'] 			= $tag;


		Debug::push( 'searches', $this->query_debug );
		unset( $this->query_debug );
		$this->query_context = array();
		$this->query_debug['current_query'] = 0;

		return $sphinx_results;
	}

	/**
	 * Rewrite AddQuery to init some debug parameters.
	 * @param $query
	 * @param string $index
	 * @param string $comment
	 * @return void
	 */
	public function AddQuery( $query, $index = "*", $comment = "" )
	{
		Benchmark::getInstance()->timingStart( 'search' );

		$this->sphinx->AddQuery( $query, $index, $comment );

		$sphinx_time = Benchmark::getInstance()->timingCurrentToRegistry( 'search' );

		$debug_sphinx = array(
			"tag" 			=> $comment,
			"query" 		=> $query,
			"connection"    => $this->sphinx_config['config_file'],
			"indexes" 		=> $index,
			"controller" 	=> $this->getCallerClass(),
			"time" 			=> $sphinx_time,
		);

		$debug_sphinx = array_merge( $debug_sphinx, $this->query_context );

		$this->query_debug['queries'][$this->query_debug['current_query']] = $debug_sphinx;
		$this->query_debug['current_query']++;
	}

	/**
	 * Rewrite Query to init some debug parameters.
	 * @param $query_filters
	 * @param $sphinx_indexes
	 * @param string $comment
	 * @return bool
	 */
	public function Query( $query_filters, $sphinx_indexes, $comment = "" )
	{
		Benchmark::getInstance()->timingStart( 'search' );

		$sphinx_results = $this->sphinx->Query( $query_filters, $sphinx_indexes, $comment );

		$sphinx_time = Benchmark::getInstance()->timingCurrentToRegistry( 'search' );

		$debug_sphinx = array(
			"tag" 			=> $comment,
			"query" 		=> $query_filters,
			"connection"    => $this->sphinx_config['config_file'],
			"indexes" 		=> $sphinx_indexes,
			"resultset" 	=> $sphinx_results,
			"time" 			=> $sphinx_time,
			"error" 		=> $this->sphinx->_error,
			"controller"	=> $this->getCallerClass(),
		);
		$debug_sphinx = array_merge( $debug_sphinx, $this->query_context );

		$this->query_debug['queries'][$this->query_debug['current_query']] = $debug_sphinx;

		$this->query_debug['time'] 			= $sphinx_time;
		$this->query_debug['error'] 		= $this->sphinx->_error;
		$this->query_debug['tag'] 			= $comment;
		$this->query_debug['total_found'] 	= $sphinx_results['total_found'];
		$this->query_debug['returned_rows'] = ( isset( $sphinx_results['matches'] ) ) ? count( $sphinx_results['matches'] ) : 0;

		Debug::push( 'searches', $this->query_debug );
		unset( $this->query_debug );
		$this->query_debug['current_query'] = 0;

		return $sphinx_results;
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
}