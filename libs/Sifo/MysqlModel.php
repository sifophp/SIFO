<?php
/**
 * LICENSE
 *
 * Copyright 2010 Carlos Soriano
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

class MysqlModel
{
	protected $db;

	/**
	 * Use this method as constructor in chidren.
	 *
	 * @return unknown
	 */
	protected function init()
	{
		return true;
	}

	/**
	 * Returns an element in the registry.
	 *
	 * @param string $key
	 * @return mixed
	 */
	protected function inRegistry( $key )
	{
		$reg = Registry::getInstance();
		if ( $reg->keyExists( $key ) )
		{
			return $reg->get( $key );
		}

		return false;
	}

	/**
	 * Stores in the registry a value with the given key.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	protected function storeInRegistry( $key, $value )
	{
		$reg = Registry::getInstance()->set( $key, $value );
	}

	/**
	 * Returns the translation of a string
	 *
	 * @param string $subject
	 * @param string $var_1
	 * @param string $var2
	 * @param string $var_n
	 * @return string
	 */
	public function translate( $subject, $var_1 = '', $var2 = '', $var_n = '' )
	{
		$args = func_get_args();
		$variables = array();
		if ( 1 < count( $args ) )
		{
			foreach ( $args as $key => $value )
			{
				$variables['%'.$key] = $value;
			}

		}

		unset( $variables['%0'] );
		return I18N::getInstance( 'messages', Domains::getInstance()->getLanguage() )->getTranslation( $subject, $variables );
	}

	/**
	 * Returns an object of the given class.
	 *
	 * @param string $class_name
	 * @param boolean $call_constructor If you want to return a 'new' instance or not. Set to false for singletons.
	 * @return Instance_of_a_Class
	 */
	public function getClass( $class_name, $call_constructor = true )
	{
		return Bootstrap::getClass( $class_name, $call_constructor );
	}

	/**
	 * Returns the Database connection object.
	 *
	 * @param string $profile The profile to be used in the database connection.
	 * @return Mysql|MysqlDebug
	 */
	protected function connectDb( $profile = 'default' )
	{
		$this->getClass( 'Mysql', false );
		if ( Domains::getInstance()->getDevMode() !== true )
		{
			return Mysql::getInstance( $profile );
		}

		$this->getClass( 'MysqlDebug', false );
		return MysqlDebug::getInstance( $profile );
	}

	/**
	 * Magic method to retrieve table names from a configuration file.
     *
     * @param string $attribute
     *
     * @return string
     */
	public function __get( $attribute )
	{
		$tablenames = Config::getInstance()->getConfig( 'tablenames' );

		$domain = Domains::getInstance()->getDomain();

		if ( isset( $tablenames['names'][$domain][$attribute] ) )
		{
			return $tablenames['names'][$domain][$attribute];
		}
		elseif ( isset( $tablenames['names']['default'][$attribute] ) )
		{
			return $tablenames['names']['default'][$attribute];
		}

		return $attribute;
	}
}