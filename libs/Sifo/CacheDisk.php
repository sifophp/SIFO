<?php
/**
 * LICENSE
 *
 * Copyright 2010 Albert Lombarte
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
 * Caching system based on Disk. Only TEXT objects can be cached.
 *
 * Serialize data before setting elements if needed.
 */
class CacheDisk
{
	const EMPTY_STRING=" ";

	private static $instance;

	public static function singleton()
	{
		self::$instance ||
				self::$instance = new CacheDisk();
		return self::$instance;
	}

	/**
	 * Set the value in disk if the value does not exist; returns FALSE if value exists
	 *
	 * @param sting $key
	 * @param mix $var
	 * @param bool $compress
	 * @param int $expire
	 * @return bool
	 */
	static public function add( $key, $contents, $compress=0, $expire=0 )
	{
		if ( self::EMPTY_STRING != self::get( $key ) )
		{
			return false;
		}
		return self::set( $key, $contents );
	}

	/**
	 * Increment an existing integer value
	 *
	 * @param string $key
	 * @param mix $value
	 * @return bool
	 */
	static function increment( $key, $value=1 )
	{
		if ( !is_int( $current_value = self::get( $key ) ) )
		{
			return false;
		}

		return self::set( $key, ( $current_value+$value ) );
	}

	/**
	 * Write content in cache
	 *
	 * @param string $key
	 * @param string $contents
	 * @param integer $compress Ignored, backwards compatibility.
	 * @param integer $expire Ignored, backwards compatibility.
	 * @return boolean
	 */
	static public function set($key, $contents, $compress=0, $expire=0 )
	{
		$final_key = self::_finalKeyGeneration( $key );
		$source_file = self::_getSystemPath( $final_key );

		if($fp = @fopen($source_file, 'w'))
		{
			fwrite($fp, $contents);
			chmod ( $source_file, 0777 );
			fclose($fp);

			return true;
		}
		else
		{
			return false;
		}
	}

	static public function get($key)
	{
		$final_key = self::_finalKeyGeneration( $key );
		$source_file = self::_getSystemPath( $final_key );

		$fp = @fopen( $source_file, 'r' );
		$filesize = @filesize($source_file);

		if ( $filesize > 0 )
		{
			$contents = fread($fp, $filesize);
			fclose($fp);

		}
		else
		{
			$contents = self::EMPTY_STRING;
		}

		return $contents;
	}

	static public function hasExpired( $key, $expiration )
	{
		$final_key = self::_finalKeyGeneration( $key );
		$source_file = self::_getSystemPath( $final_key );

		if( !file_exists( $source_file ) || !( $mtime = filemtime( $source_file ) ) )
		{
			return true;
		}

		// Cache expired?
		if( ( $mtime + $expiration ) < time() )
		{
			@unlink( $source_file );
			return true;
		}

		return false;
	}

	static public function delete( $key )
	{
		$final_key = self::_finalKeyGeneration( $key );
		$source_file = self::_getSystemPath( $final_key );
		@unlink( $source_file );
	}

	/**
	 * Generate the cache key to be used in the save disk process.
	 * This function was created for avoid bug trying to write utf8 chars like filename.
	 *
	 * @param <type> $suggested_key
	 */
	static private function _finalKeyGeneration( $key )
	{
		 $final_key  = preg_replace( '/[^0-9a-z_\-]/', '', strtolower( $key ) ).'-'.sha1( $key );

		 return $final_key;
	}


	static private function _getSystemPath( $key )
	{
		$key = str_replace( '..', '', $key );
		return ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/_smarty/cache/' . $key .'.cached.html';
	}
}