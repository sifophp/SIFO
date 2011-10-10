<?php

class CoverageAnalysis
{
	static protected $files;
	static protected $coverage_started = false;

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

	static public function getFiles()
	{
		return self::$files;
	}

	static public function start()
	{
		if ( count( self::$files ) <= 0 || !self::isEnabled() )
		{
			return null;
		}

		xdebug_start_code_coverage( XDEBUG_CC_UNUSED );
		self::$coverage_started = true;
	}

	static public function getStop()
	{
		$coverage = array();

		if ( ( false !== self::$coverage_started ) && ( false !== self::isEnabled() ) )
		{
			$coverage = xdebug_get_code_coverage();
			xdebug_stop_code_coverage();
		}

		return $coverage;
	}

	static public function stop()
	{
		if ( ( false !== self::$coverage_started ) && ( false !== self::isEnabled() ) )
		{
			xdebug_stop_code_coverage();
		}
	}

	static public function isEnabled()
	{
		return defined( 'XDEBUG_CC_UNUSED' );
	}
}

?>
