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
	const TIME_LIMIT = 10; // The Lock is autoreleased 10 secs after it was locked.
	const LOCK_VALIDATION_TIME = 500000; // Validate the page generation every 0,5 secs.

	protected $lock_id;
	protected $key;
	private static $instances;
	protected $cache;

	private function __construct( $key, $cache_instance )
	{
		$this->lock_id = rand( 0, 1000 ).'::'.time();
		$this->key = 'LOCK::'.$key;

		$this->cache = $cache_instance;
	}

	public static function getInstance( $key, $cache_instance )
	{
		if ( !isset( self::$instances[$key] ) )
		{
			self::$instances[$key] = new self( $key, $cache_instance );
		}

		return self::$instances[$key];
	}

	function isLocked()
	{
		// The flow is not locked if the current process is the lock holder.
		return ( ( $value = $this->cache->get( $this->key, false ) ) && ( $value != $this->lock_id ) );
	}

	function hold()
	{
		$this->cache->set( $this->key, $this->lock_id, self::TIME_LIMIT );
	}

	function release()
	{
		return ( $this->cache->delete( $this->key ) );
	}
}
