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
			return $this->cache_object->get( $key );
		}
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
			$pointer = $this->get( sprintf( self::CACHE_TAG_STORE_FORMAT, $tag, $value ) );
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
