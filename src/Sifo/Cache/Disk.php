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
 *	 http://www.apache.org/licenses/LICENSE-2.0
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
class CacheDisk extends CacheBase
{
	/**
	 * Creates the CacheDisk object and inits the "cache_object" used by parent.
	 */
	public function __construct()
	{
		// CacheBase uses this attribute, for proxy access:
		$this->cache_object = &$this;
		return $this;
	}

	/**
	 * Stores variable "var" with "key" only if such key doesn't exist at the server yet.
	 *
	 * @param string $key
	 * @param mixed $var
	 * @param integer $expire Seconds until this item will expire. Zero for persistent caching (never expire).
	 *
	 * @return bool
	 */
	public function add( $key, $var, $expire = 0 )
	{
		if ( false != $this->get( $key ) )
		{
			return false;
		}
		return $this->set( $key, $var, $expire );
	}


	/**
	 * Increment an existing integer value
	 *
	 * @param string $key
	 * @param integer $value
	 * @param integer $expire Seconds until this item will expire. Zero for persistent caching (never expire).
	 *
	 * @return bool
	 */
	public function increment( $key, $value = 1, $expire = 0 )
	{
		if ( !is_numeric( $current_value = $this->get( $key ) ) )
		{
			return false;
		}

		$new_value = $current_value + $value;
		return ( $this->set( $key, $new_value, $expire ) ? $new_value : false );
	}

	/**
	 * Increment an existing integer value
	 *
	 * @param string $key
	 * @param integer $value
	 * @param integer $expire Seconds until this item will expire. Zero for persistent caching (never expire).
	 * @return bool
	 */
	public function decrement( $key, $value = 1, $expire = 0 )
	{
		if ( !is_numeric( $current_value = $this->get( $key ) ) )
		{
			return false;
		}

		$new_value = $current_value - $value;
		return ( $this->set( $key, $new_value, $expire ) ? $new_value : false );
	}

	/**
	 * Write content in cache
	 *
	 * @param string $key
	 * @param string $contents
	 * @param integer $expire Seconds until this item will expire. Zero for persistent caching (never expire).
	 *
	 * @return boolean
	 */
	public function set( $key, $contents, $expire = 0 )
	{
		$source_file = $this->getCacheFilename( $key );

		$cache_content = array(
			'key' => $key,
			'expiration' => $expire,
			'content' => $contents
		);
		return file_put_contents( $source_file, serialize( $cache_content ) );
	}

	/**
	 * Returns the content associated to that key or FALSE.
	 *
	 * Also deletes the cache file if has expired when checking it.
	 *
	 * @param string $key
	 *
	 * @return mixed Cached content or FALSE.
	 */
	public function get( $key )
	{
		if ( $this->hasRebuild() )
		{
			return false;
		}

		$source_file = $this->getCacheFilename( $key );

		$file_content = @file_get_contents( $source_file );
		if ( !$file_content )
		{
			return false;
		}

		$cache_content = @unserialize($file_content);

		if ( !isset( $cache_content['expiration'] ) || !isset( $cache_content['content'] ) )
		{
			return false;
		}

		// Check if content has expired (expiration=0 means persistent cache):
		if ( $cache_content['expiration'] > 0 )
		{
			$mtime = filemtime( $source_file );
			if ( ( $mtime + $cache_content['expiration'] ) < time() )
			{
				// Delete the file.
				@unlink( $source_file );
				return false;
			}
		}

		return $cache_content['content'];


	}

	/**
	 * Deletes the cache key.
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function delete( $key )
	{
		return @unlink( $this->getCacheFilename( $key ) );
	}


	/**
	 * Check if cache to Disk will work.
	 *
	 * @return integer
	 */
	public function isActive()
	{
		$cache_path = $this->getPathBase();
		return is_writable( $cache_path ) && is_readable( $cache_path );
	}


	/**
	 * Returns the full path to the cache filename related to the given key.
	 *
	 * @param $key
	 *
	 * @return string
	 */
	private function getCacheFilename( $key )
	{
		$hash = sha1( $key );
		return $this->getPathBase() . "{$hash}.cache";
	}

	/**
	 * Returns the base folder where cache will be stored.
	 *
	 * @return string
	 */
	private function getPathBase()
	{
		return ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/_smarty/cache/';
	}
}
