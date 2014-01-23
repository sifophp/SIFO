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

/**
 * Global storage of objects in memory. They expire after script execution.
 */
class Registry
{

	/**
	 * Registry object provides storage for shared objects.
	 */
	private static $instance = null;

	/**
	 * Array where all the storage is done.
	 *
	 * @var array
	 */
	private static $storage = array();

	/**
	 * Retrieves the default registry instance.
	 *
	 * @return Registry
	 */
	public static function getInstance()
	{
		if ( self::$instance === null )
		{
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Avoid external construction of class without singleton usage.
	 *
	 */
	private function __construct()
	{

	}

	/**
	 * Get a value from the registry.
	 *
	 * @param string $key Name you used to store the value.
	 * @return mixed
	 */
	public static function get( $key )
	{
		$instance = self::getInstance();

		if ( self::keyExists( $key ) )
		{
			return self::$storage[$key];
		}
		return false;
	}

	/**
	 * Stores the object with the name given in $key.
	 *
	 * @param string $key Name you want to store the value with.
	 * @param mixed $value The object to store in the array.
	 * @return void
	 */
	public static function set( $key, $value )
	{
		self::$storage[$key] = $value;
	}

	/**
	 * Unset the object with the name given in $key.
	 *
	 * @param string $key Name you want to store the value with.
	 * @return void
	 */
	public static function invalidate( $key )
	{
		if ( isset( self::$storage[$key] ) )
		{
			unset( self::$storage[$key] );
		}
	}

	/**
	 * Stores the object with the name given in $key and $sub_key.
	 *
	 * Example: array( $key => array( $subkey => $value ) )
	 *
	 * @param string $key Name you want to store the value with.
	 * @param mixed $value The object to store in the array.
	 * @return void
	 */
	public static function subSet( $key, $sub_key, $value  )
	{
		self::$storage[$key][$sub_key] = $value;
	}

	/**
	 * Adds another element to the end of the array.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return int New number of elements in the array.
	 */
	public static function push( $key, $value )
	{
		if ( !self::keyExists( $key ) )
		{
			self::$storage[$key] = array();
		}

		if ( !is_array( self::$storage[$key] ) )
		{
			throw new RegistryException( 'Failed to PUSH an element in the registry because the given key is not an array.' );
		}

		return array_push( self::$storage[$key], $value );
	}

	/**
	 * @param string $index
	 * @returns boolean
	 *
	 */
	public static function keyExists( $key )
	{
		return array_key_exists( $key, self::$storage );
	}

}