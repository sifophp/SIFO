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

use Stash\Driver;
use Stash\Interfaces;
use Stash\Pool;

/**
 * Proxy class that handles all Cache types in a single interface.
 */
class Cache
{
	const CACHE_TYPE_AUTODISCOVER = 'AUTODISCOVER';
	const CACHE_TYPE_MEMCACHED = 'MEMCACHED';
	const CACHE_TYPE_MEMCACHE = 'MEMCACHE';
	const CACHE_TYPE_REDIS = 'REDIS';
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
	 *
	 * @return CacheClient
	 *
	 * @throws Exception_500
	 */
	static public function getInstance( $type = self::CACHE_TYPE_AUTODISCOVER )
	{
        if ( self::CACHE_TYPE_AUTODISCOVER == $type )
        {
            $type = self::discoverCacheType();
        }

		self::$cache_type = $type;

		if ( isset( self::$instance[self::$cache_type] ) )
		{
			return self::$instance[self::$cache_type];
		}

		switch ( self::$cache_type )
		{
			case self::CACHE_TYPE_MEMCACHED:
			case self::CACHE_TYPE_MEMCACHE:
                $servers = Config::getInstance()->getConfig( 'cache', 'servers' );
                $servers = $servers[0];

                $options = [];
                foreach ($servers as $server_host => $server_port)
                {
                    $options['servers'][] = [$server_host, $server_port];
                }

                $cache_driver = new Driver\Memcache($options);
				break;
			case self::CACHE_TYPE_REDIS:
                $servers = Config::getInstance()->getConfig( 'cache', 'servers' );
                $servers = $servers[0];

                $options = [];
                foreach ($servers as $server_host => $server_port)
                {
                    $options['servers'][] = [$server_host, $server_port];
                }

				$cache_driver = new Driver\Redis($options);
				break;
			case self::CACHE_TYPE_DISK:
                $options['path'] = ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/_smarty/cache/';
                $cache_driver    = new Driver\FileSystem($options);
				break;
			default:
				throw new Exception_500("Unknown cache type requested: '" . self::$cache_type . "'");
		}

		$cache_pool = new Pool($cache_driver);

		if (!self::isCacheDriverAvailable($cache_pool))
		{
			self::$cache_type = self::CACHE_TYPE_DISK;
			$options['path']  = ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/_smarty/cache/';
			$cache_driver     = new Driver\FileSystem($options);
			$cache_pool       = new Pool($cache_driver);
		}

		self::$instance[self::$cache_type] = new CacheClient($cache_pool);

		return self::$instance[self::$cache_type];
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

		$cache_client = mb_strtolower($cache_config['client']);

		if ( 'memcached' == $cache_client )
		{
			return self::CACHE_TYPE_MEMCACHED;
		}

		if ( 'memcache' == $cache_client )
		{
			return self::CACHE_TYPE_MEMCACHE;
		}

		if ( 'redis' == $cache_client )
		{
			return self::CACHE_TYPE_REDIS;
		}

		return 'unknown';
	}

	private static function isCacheDriverAvailable(Pool $cache_pool)
	{
		if ($cache_pool->getDriver() instanceof Driver\FileSystem)
		{
			return true;
		}

		try
		{
			// Try to save an item...
			$availability_item = $cache_pool->getItem('SifoCacheAvailability');
			$availability_item->lock();
			$availability_item->set(true);
			$cache_pool->save($availability_item);

			// ...and immediately try to recover it from cache.
			$availability_item = $cache_pool->getItem('SifoCacheAvailability');
			$is_available = $availability_item->get();

			// If it returns data, and data is `true`, it is available!
			return $is_available;
		}
		catch (\Exception $exception)
		{
			return false;
		}
	}
}
