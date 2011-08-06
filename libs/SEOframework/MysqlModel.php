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

class MysqlModel extends Model
{
	protected $db;

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
	 * Magic method to retreive table names from a configuration file.
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
?>