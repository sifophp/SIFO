<?php
/**
 * LICENSE
 *
 * Copyright 2012 Albert Lombarte
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
 * Common methods available to every Cache instance.
 */
class CacheBase
{
	/**
	 * Define the format of the stored cache tag.
	 *
	 * @var string
	 */
	const CACHE_TAG_STORE_FORMAT = '!tag-%s=%s';

	/**
	 * Contains all the tags together at their current version.
	 *
	 * This cache key avoids asking every TAG to cache and concentrate them all in a single call.
	 */
	const CACHE_TAG_VERSIONS = '!tags-versions-%s';

	/**
	 * Contains the original cache object.
	 *
	 * @var null
	 */
	protected $cache_object = null;

	/**
	 * Keeps a copy of all the tags versions.
	 *
	 * @var array
	 */
	static $tags = null;

	/**
	 * Derive all unknown/unimplemented calls to the original cache object.
	 *
	 * @param string $method
	 * @param mixed $args
	 *
	 * @return mixed
	 */
	public function __call( $method, $args ) //call adodb methods
	{
		return call_user_func_array( array(
			$this->cache_object,
			$method
		), $args );
	}

	/**
	 * Check if Memcache is active and right connected
	 *
	 * @return integer
	 */
	public function isActive()
	{
		return ( false != $this->cache_object->getVersion() );
	}

	/**
	 * Returns if current execution allows rebuilding the page.
	 *
	 * @return bool
	 */
	public function hasRebuild()
	{
		return Domains::getInstance()->getDevMode() && ( FilterGet::getInstance()->getInteger( 'rebuild' ) || FilterCookie::getInstance()->getInteger( 'rebuild_all' ) );
	}

	/**
	 * Returns the content of the cache "key".
	 *
	 * @param $key
	 *
	 * @return mixed Cache content or false.
	 */
	public function get( $key )
	{
		if ( $this->hasRebuild() )
		{
			return false;
		}
		else
		{
			$sha1 = sha1( $key );
			if ( !( $content = $this->cache_object->get( $sha1 ) ) )
			{
				$lock = CacheLock::getInstance( $sha1, $this->cache_object );

				if ( $lock->isLocked() )
				{
					do
					{
						usleep( CacheLock::WAIT_TIME );
					}
					while( $lock->isLocked() );

					if ( !( $content = $this->cache_object->get( $sha1 ) ) )
					{
						trigger_error( "Cache lock timeout.Lock for $key (SHA1: $sha1) has not released after ".CacheLock::TTL." seconds of script running.", E_USER_WARNING );
					}
				}
				else
				{
					$lock->acquire();
				}
			}

			// Check for any possible SHA1 collisions:
			if ( isset( $content['content'] ) && $content['key'] == $key )
			{
				return $content['content'];
			}

			return false;
		}
	}

	/**
	 * Stores "$content" under "$key" for "$expiration" seconds.
	 *
	 * @param $key string
	 * @param $content mixed
	 * @param $expiration integer
	 * @return boolean
	 */
	public function set( $key, $content, $expiration )
	{
		$content = array(
			'key' => $key,
			'content' => $content,
			//'expiration' => $expiration,
			//'time' => time()
		);

		$key = sha1( $key );
		$set_result =  $this->cache_object->set( $key, $content, $expiration );

		CacheLock::getInstance( $key, $this->cache_object )->release();

		return $set_result;
	}

	/**
	 * Construct the cache tag if it's defined in config.
	 *
	 * @param string $tag Cache tag.
	 * @param mixed $value Cache value.
	 *
	 * @return string
	 */
	public function getCacheTag( $tag, $value )
	{
		$cache_tag = $tag . '=' . $value;

		$cache_config = Config::getInstance()->getConfig( 'cache' );

		if ( isset( $cache_config['cache_tags'] ) && in_array( $tag, $cache_config['cache_tags'] ) )
		{
			if ( !( $pointer = $this->cache_object->get( $key_tag = sprintf( self::CACHE_TAG_STORE_FORMAT, $tag, $value ) ) ) )
			{
				// Default declaration when the tag is not initialized.
				// This code piece is required to the cache lock release.
				$this->cache_object->set( $key_tag, 0, 0 ); // $expiration = 0 => Unexpirable.
			}
			$cache_tag .= '/' . ( int ) $pointer;
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
	public function getCacheKeyName( Array $definition )
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
				$cache_key[] = $this->getCacheTag( $key, $val );
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
	public function deleteCacheByTag( $tag, $value )
	{
		$stored_tag = sprintf( self::CACHE_TAG_STORE_FORMAT, $tag, $value );

		if ( false === $this->add( $stored_tag, 1 ) )
		{
			$this->increment( $stored_tag );
		}

		return true;
	}
}
