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

class Cache
{
	static private $instance;
	static public $cache_type;

	private function __construct() {}

	/**
	 * Singleton of config class.
	 *
	 * @return object Config
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
	 * Delegate all calls to the proper class.
	 *
	 * @param string $method
	 * @param mixed $args
	 * @return mixed
	 */
	function __call($method, $args)//call adodb methods
	{
		return call_user_func_array(array(self::$instance, $method),$args);
	}
}