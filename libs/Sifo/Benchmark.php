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

/**
 * Benchmarking for PHP applications.
 */
class Benchmark
{
	private static $instance;
	public static $start_times;
	public static $stop_times;
	public static $delta_points;

	/**
	 * Singleton of benchmark class.
	 *
	 * @return Benchmark
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
	 * Class constructor.
	 */
	public function __construct()
	{
		self::$start_times = array();
		self::$stop_times = array();
		self::$delta_points = array();
	}

	/**
	 * Starts the timer for the given group name.
	 *
	 * @param string $name
	 */
	public function timingStart ($name = 'default') {
		self::$start_times[$name] = explode(' ', microtime());
	}

	/**
	 * Stops the timer for the given group name.
	 *
	 * @param string $name
	 */
	public function timingStop ($name = 'default') {
		self::$stop_times[$name] = explode(' ', microtime());
	}

	/**
	 * Returns the current time for the given group name.
	 *
	 * @param string $name
     * @return int Current time
     */
	public function timingCurrent ($name = 'default') {
		if (!isset(self::$start_times[$name])) {
			return 0;
		}
		if (!isset(self::$stop_times[$name])) {
			$stop_time = explode(' ', microtime());
		}
		else {
			$stop_time = self::$stop_times[$name];
		}
		// do the big numbers first so the small ones aren't lost
		$current = $stop_time[1] - self::$start_times[$name][1];
		$current += $stop_time[0] - self::$start_times[$name][0];
		return $current;
	}

	/**
	 * Stores the current time to registry to accumulate partials.
	 *
	 * @param string $name
	 * @return int
	 */
	public function timingCurrentToRegistry ($name = 'default')
	{
		$num_elements = Debug::get( 'elements' );
		if ( isset( $num_elements[$name] ) )
		{
			$num_elements = $num_elements[$name] + 1;
		}
		else
		{
			$num_elements = 1;
		}

		Debug::subSet( 'elements', $name, $num_elements );

		$total_times = Debug::get( 'times' );

		$actual_time = self::timingCurrent($name);

		if ( isset( $total_times[$name] ) )
		{
			$total_times = $total_times[$name] + $actual_time;
		}
		else
		{
			$total_times = $actual_time;
		}


		Debug::subSet( 'times', $name, $total_times );

		unset( self::$start_times[$name] );
		unset( self::$stop_times[$name] );

		return $actual_time;
	}
}
?>