<?php

class CoverageAnalysis
{
	static protected $files;

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
}

?>
