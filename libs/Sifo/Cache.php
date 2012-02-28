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

class Cache
{
	/**
	 * Define the format of the stored cache tag.
	 *
	 * @var string
	 */
	const CACHE_TAG_STORE_FORMAT = '!tag-%s=%s';

	static private $instance;
	static public $cache_type;

	private function __construct()
	{
	}

	/**
	 * Singleton of config class.
	 *
	 * @return Cache
	 */
	public static function getInstance()
	{
		if ( !isset ( self::$instance ) )
		{
			$memcache_config = Config::getInstance()->getConfig( 'memcache' );
			// Check if Memcached is enabled by configuration:
			if ( true === $memcache_config['active'] )
			{
				if ( isset( $memcache_config['client'] ) && $memcache_config['client'] === 'Memcached' )
				{
					// Use the newer client library MemcacheD.
					include_once ROOT_PATH . '/libs/MemCached/memcached.class.php';
					$memcached = \Sifo\MemcachedClient::getInstance();
				}
				else
				{
					// Use the old client library Memcache.
					include_once ROOT_PATH . '/libs/MemCached/memcache.class.php';
					$memcached = \MemcacheClient::getInstance();
				}

				// Check that Memcached is listening:
				if ( $memcached->isActive() )
				{
					self::$instance = $memcached;
					self::$cache_type = 'Memcached';
				}
				else
				{
					trigger_error( 'Memcached is down!' );
					// Failed to connect to Memcached hosts
					$memcache_config['active'] = false;

					// Use cache disk instead:
					include ROOT_PATH . '/libs/Sifo/CacheDisk.php';
					self::$instance = CacheDisk::singleton();
					self::$cache_type = 'Disk';
				}
			}
			else
			{
				// Use cache disk instead:
				include ROOT_PATH . '/libs/Sifo/CacheDisk.php';
				self::$instance = CacheDisk::singleton();
				self::$cache_type = 'Disk';
			}
		}

		return self::$instance;
	}

	/**
	 * Construct the cache tag if it's defined in config.
	 *
	 * @param string $tag Cache tag.
	 * @param mixed $value Cache value.
	 *
	 * @return string
	 */
	public static function getCacheTag( $tag, $value )
	{
		$cache_tag = $tag . '=' . $value;

		$cache_config = Config::getInstance()->getConfig( 'memcache' );

		if ( isset( $cache_config['cache_tags'] ) && in_array( $tag, $cache_config['cache_tags'] ) )
		{
			$cache_handler = Cache::getInstance();
			$pointer = $cache_handler->get( sprintf( self::CACHE_TAG_STORE_FORMAT, $tag, $value ) );
			$cache_tag .= '/' . ( int )$pointer;
		}

		return $cache_tag;
	}

	/**
	 * Returns the cache string identifier after calculating all the tags and prepending the necessary attributes.
	 *
	 * @param array $definition Cache definition.
	 *
	 * @return string
	 */
	public static function getCacheKeyName( Array $definition )
	{
		$cache_key = array();
		$cache_base_key = array();

		// First of all, let's construct the cache base with domain, language and controller name.
		$cache_base_key[] = Domains::getInstance()->getDomain();
		$cache_base_key[] = Domains::getInstance()->getLanguage();

		// Now we add the rest of identifiers of the definition excluding the "expiration".
		unset( $definition['expiration'] );

		if ( !empty( $definition ) )
		{
			foreach ( $definition as $key => $val )
			{
				$cache_key[] = Cache::getCacheTag( $key, $val );
			}
			sort( $cache_key );
		}

		return implode( '_', array_merge( $cache_base_key, $cache_key ) );
	}

	/**
	 * Delete cache from all the keys that contain the given tag in that value.
	 *
	 * @param string $tag Cache tag.
	 * @param mixed $value Cache value.
	 *
	 * @return boolean Always returns true
	 */
	public static function deleteCacheByTag( $tag, $value )
	{
		$stored_tag = sprintf( self::CACHE_TAG_STORE_FORMAT, $tag, $value );
		$cache_handler = Cache::getInstance();

		if ( false === $cache_handler->add( $stored_tag, 1 ) )
		{
			$cache_handler->increment( $stored_tag );
		}

		return true;
	}

	/**
	 * Delegate all calls to the proper class.
	 *
	 * @param string $method
	 * @param mixed $args
	 *
	 * @return mixed
	 */
	public function __call( $method, $args ) //call adodb methods
	{
		return call_user_func_array( array(
			self::$instance,
			$method
		), $args );
	}
}