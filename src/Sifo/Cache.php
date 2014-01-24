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
class Cache extends CacheBase
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
	 * @return Cache
	 */
	static public function getInstance( $type = self::CACHE_TYPE_AUTODISCOVER, $lock_enabled = self::CACHE_LOCKING_ENABLED )
	{

		if ( !isset ( self::$instance[$type] ) )
		{
			if ( self::CACHE_TYPE_AUTODISCOVER == $type )
			{
				$type = self::discoverCacheType();
			}

			switch ( $type )
			{
				case self::CACHE_TYPE_MEMCACHED:
					// http://php.net/manual/en/book.memcached.php
					// Memcached offers more methods than Memcache (like append, cas, replaceByKey...)
					self::$instance[$type][$lock_enabled] = new CacheMemcached();
					break;
				case self::CACHE_TYPE_MEMCACHE:
					// http://php.net/manual/en/book.memcache.php:
					self::$instance[$type][$lock_enabled] = new CacheMemcache();
					break;
				case self::CACHE_TYPE_DISK:
					// Use cache disk instead:
					self::$instance[$type][$lock_enabled] = new CacheDisk();
					break;
				default:
					throw new Exception_500( 'Unknown cache type requested' );
			}

			self::$cache_type = $type;

			// Memcache is down, we cache on disk to handle this dangerous situation:
			if ( false !== strpos( self::$cache_type, 'MEMCACHE' ) && !self::$instance[$type][$lock_enabled]->isActive() )
			{
				trigger_error( 'Memcached is down! Falling back to Disk cache if available...' );

				// Use cache disk instead:
				self::$instance[$type][$lock_enabled] = new CacheDisk();
				self::$cache_type = self::CACHE_TYPE_DISK;
			}

		}

		self::$instance[$type][$lock_enabled]->lock_enabled = $lock_enabled;

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

		if ( true === $cache_config['active'] )
		{
			if ( isset( $cache_config['client'] ) && $cache_config['client'] === 'Memcached' )
			{
				$type = self::CACHE_TYPE_MEMCACHED;
			}
			else
			{
				$type = self::CACHE_TYPE_MEMCACHE;
			}
		}
		else
		{
			$type = self::CACHE_TYPE_DISK;
		}

		return $type;
	}


}