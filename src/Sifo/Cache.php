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
 * Proxy class that handles all Cache types in a single interface.
 */
class Cache
{
	const CACHE_TYPE_AUTODISCOVER = 'AUTODISCOVER';
	const CACHE_TYPE_MEMCACHED = 'MEMCACHED';
	const CACHE_TYPE_MEMCACHE = 'MEMCACHE';
	const CACHE_TYPE_DISK = 'DISK';

	const CACHE_LOCKING_ENABLED = 1;
	const CACHE_LOCKING_DISABLED = 0;

	static private $instance;
	static public $cache_type;

	private function __construct()
	{
	}

	/**
	 * Singleton of config class.
	 *
	 * @param string $type
	 * @param int    $lock_enabled
	 *
	 * @return CacheBase
	 *
	 * @throws Exception_500
	 */
	static public function getInstance( $type = self::CACHE_TYPE_AUTODISCOVER, $lock_enabled = self::CACHE_LOCKING_DISABLED )
	{
        if ( self::CACHE_TYPE_AUTODISCOVER == $type )
        {
            $type = self::discoverCacheType();
        }

		self::$cache_type = $type;

		if ( isset( self::$instance[$type][$lock_enabled] ) )
		{
			return self::$instance[$type][$lock_enabled];
		}

		switch ( $type )
		{
			case self::CACHE_TYPE_MEMCACHED:
				// http://php.net/manual/en/book.memcached.php
				// Memcached offers more methods than Memcache (like append, cas, replaceByKey...)
				$cache_object = new CacheMemcached();
				break;
			case self::CACHE_TYPE_MEMCACHE:
				// http://php.net/manual/en/book.memcache.php:
				$cache_object = new CacheMemcache();
				break;
			case self::CACHE_TYPE_DISK:
				// Use cache disk instead:
				$cache_object = new CacheDisk();
				break;
			default:
				throw new Exception_500( "Unknown cache type requested: '{$type}'");
		}

		// Memcache is down, we cache on disk to handle this dangerous situation:
		if ( false !== strpos( self::$cache_type, 'MEMCACHE' ) && !$cache_object->isActive() )
		{
			trigger_error( 'Memcached is down! Falling back to Disk cache if available...' );

			// Use cache disk instead:
			$cache_object     = new CacheDisk();
			self::$cache_type = self::CACHE_TYPE_DISK;
		}

		$cache_object->use_locking            = (bool) $lock_enabled;
		self::$instance[$type][$lock_enabled] = $cache_object;

		return self::$instance[$type][$lock_enabled];
	}

	/**
	 * Reads from configuration files the cache type this project is using by default.
	 *
	 * @return string Cache type.
	 */
	static protected function discoverCacheType()
	{
		$cache_config = Config::getInstance()->getConfig( 'cache' );

		if ( true !== $cache_config['active'] || !isset( $cache_config['client'] ) )
		{
			return self::CACHE_TYPE_DISK;
		}

		if ( 'Memcached' === $cache_config['client'] )
		{
			return self::CACHE_TYPE_MEMCACHED;
		}

		if ( 'Memcache' === $cache_config['client'] )
		{
			return self::CACHE_TYPE_MEMCACHE;
		}

		return 'unknown';
	}
}
