<?php
/**
 * CoverageAnalysis
 *
 * LICENSE
 *
 * Copyright 2011 Jorge Tarrero
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
 * @package    SEOFramework
 * @author     Jorge Tarrero
 * @copyright  2011 Jorge Tarrero
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @link       http://www.sifo.me/
 */
class CoverageAnalysis
{
	/**
	 * List of files to be analysed.
	 *
	 * @var array
	 */
	static protected $files;

	/**
	 * Enabled if the code coverage has started.
	 *
	 * @var boolean
	 */
	static protected $coverage_started = false;

	/**
	 * Add a file to the list.
	 *
	 * @access public
	 * @param string $file Full path to the file.
	 */
	static public function add( $file )
	{
		if ( !isset( self::$files ) )
		{
			self::$files = array();
		}

		if ( !in_array( $file, self::$files ) )
		{
			include_once( $file );
			self::$files[] = realpath( $file );
		}
	}

	/**
	 * Remove a file from the list.
	 *
	 * @access public
	 * @param string $file Full path to the file.
	 */
	static public function remove( $file )
	{
		if ( !isset( self::$files ) )
		{
			self::$files = array();
		}


		if ( false !== ( $key = array_search( realpath( $file ), self::$files ) ) )
		{
			unset( self::$files[$key] );
		}
	}

	/**
	 * Return the list of analysed files.
	 *
	 * @access public
	 * @return array
	 */
	static public function getFiles()
	{
		return self::$files;
	}

	/**
	 * Start XDebug code coverage.
	 *
	 * @access public
	 * @return null if XDebug is not present.
	 */
	static public function start()
	{
		if ( count( self::$files ) <= 0 || !self::isEnabled() )
		{
			return null;
		}

		xdebug_start_code_coverage( XDEBUG_CC_UNUSED );
		self::$coverage_started = true;
	}

	/**
	 * Get the XDebug code coverage report.
	 *
	 * @access public
	 * @return array
	 */
	static public function getStop()
	{
		$coverage = array();

		if ( ( false !== self::$coverage_started ) && ( false !== self::isEnabled() ) )
		{
			$coverage = xdebug_get_code_coverage( XDEBUG_CC_UNUSED );
			xdebug_stop_code_coverage();
		}

		return $coverage;
	}

	/**
	 * Stop XDebug code coverage.
	 *
	 * @access public
	 */
	static public function stop()
	{
		if ( ( false !== self::$coverage_started ) && ( false !== self::isEnabled() ) )
		{
			xdebug_stop_code_coverage();
		}
	}

	/**
	 * Return status of XDebug extension.
	 *
	 * @access public
	 * @return boolean
	 */
	static public function isEnabled()
	{
		return defined( 'XDEBUG_CC_UNUSED' );
	}
}

?>
