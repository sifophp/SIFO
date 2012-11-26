<?php
/**
 * LICENSE
 *
 * Copyright 2012 Sergi Ambel
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

class CacheLock
{
	/**
	 * Maximum time a lock is effective.
	 */
	const TTL = 8;

	/**
	 * Time until the next locking check is performed (in microseconds).
	 */
	const WAIT_TIME = 100000; // 10 per second

	/**
	 * Cache key prefix.
	 */
	const KEY_PREFIX = '$LOCK$';

	protected $lock_id;
	protected $key;
	private static $instances;
	protected $cache_object;

	private function __construct( $key, $cache_instance )
	{
		$this->lock_id =  uniqid();
		$this->key = self::KEY_PREFIX . $key;

		$this->cache_object = $cache_instance;
	}

	public static function getInstance( $key, $cache_instance )
	{
		if ( !isset( self::$instances[$key] ) )
		{
			self::$instances[$key] = new self( $key, $cache_instance );
		}

		return self::$instances[$key];
	}

	/**
	 * Returns if another cache calculation is in progress.
	 *
	 * @return bool
	 */
	public function isLocked()
	{
		// The flow is not locked if the current process is the lock holder.
		return ( ( $value = $this->cache_object->get( $this->key ) ) && ( $value != $this->lock_id ) );
	}


	/**
	 * Acquire lock.
	 *
	 *return @boolean
	 */
	public function acquire()
	{
		$this->cache_object->set( $this->key, $this->lock_id, self::TTL );
	}

	/**
	 * Releases the lock.
	 *
	 * @return boolean
	 */
	public function release()
	{
		return ( $this->cache_object->delete( $this->key ) );
	}
}
