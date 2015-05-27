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

/**
 * Common methods available to every Cache instance.
 */
class CacheMemcacheAdapter extends Memcache
{

	/**
	 * Stores the given "content" under the key "$key" on the memcached server.
	 *
	 * Parameter expire is expiration time in seconds. If it's 0, the item never expires (but memcached server doesn't
	 * guarantee this item to be stored all the time, it could be deleted from the cache to make place for other items)
	 *
	 * The params order is changed to make it compatible with the rest of caching systems.
	 *
	 * @param string $key
	 * @param mixed $content
	 * @param integer $expire Timestamp or number of seconds until expiration. If passed in seconds value over 30 days is not understood.
	 *
	 * @return boolean True on success or false on failure.
	 */
	public function set( $key, $content, $expire = 0 )
	{
		// Compression parameter is not needed in the framework implementation, also it does not work well with small values.
		$compress = 0; // or MEMCACHE_COMPRESSED for compression.

		return parent::set( $key, $content, $compress, $expire );
	}
}
