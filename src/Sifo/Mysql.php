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

use PDO;

/**
 * Database class. Uses PDO.
 */
class Mysql
{
	/**
	 * The singleton instance of this class.
	 *
	 * @var Db Object.
	 */
	static private $instance = NULL;

	/**
	 * The PDO instance.
	 *
	 * @var PDO Object.
	 */
	protected $pdo;

	/**
	 * The domains.config params related to the database.
	 *
	 * @var array
	 */
	protected $db_params;

	/**
	 * The statement class that will be used.
	 *
	 * @var string
	 */
	const STATEMENT_CLASS = '\\Sifo\\MysqlStatement';

	/**
	 * Initializes the PDO object with the domains.config.php database configuration.
	 *
	 * @param string $profile The database server to connect to.
	 */
	public function __construct( $profile )
	{
		$this->db_params = Domains::getInstance()->getDatabaseParams();
		$init_commands = array();

		if ( !empty( $this->db_params['db_init_commands'] ) )
		{
			$init_commands = array( PDO::MYSQL_ATTR_INIT_COMMAND => implode( ';', $this->db_params['db_init_commands'] ) );
		}

		$this->pdo = new PDO(
			"mysql:host={$this->db_params['db_host']};dbname={$this->db_params['db_name']}",
			$this->db_params['db_user'],
			$this->db_params['db_password'],
			$init_commands

		);
		$class = get_called_class();
		$this->pdo->setAttribute( PDO::ATTR_STATEMENT_CLASS, array( $class::STATEMENT_CLASS, array( $this->pdo, $profile ) ) );
	}

	/**
	 * Singleton static method.
	 *
	 * @param string $profile The database server to connect to.
	 * @return Db
	 */
	static public function getInstance( $profile = 'default' )
	{
		if ( !isset( self::$instance[$profile] ) )
		{
			Benchmark::getInstance()->timingStart( 'db_connections' );

			self::$instance[$profile] = new Mysql( $profile );

			Benchmark::getInstance()->timingCurrentToRegistry( 'db_connections' );
		}
		return self::$instance[$profile];
	}

	/**
	 * Calls the pdo query method.
	 *
	 * @param string $statement The query statement to be executed in the database server.
	 * @param string $context Used in debug to identify the query context.
	 * @return PDOStatament
	 */
	public function query($statement)
	{
		return $this->pdo->query( $statement );
	}

	/**
	 * Prepares a statement.
	 *
	 * @param string $statement This must be a valid SQL statement for the target database server.
	 * @param array $driver_options This array holds one or more key=>value pairs to set attribute values for the PDOStatement object that this method returns. You would most commonly use this to set the PDO::ATTR_CURSOR value to PDO::CURSOR_SCROLL to request a scrollable cursor. Some drivers have driver specific options that may be set at prepare-time.
	 * @return DbStatement
	 */
	public function prepare( $statement, $driver_options = array() )
	{
		return $this->pdo->prepare( $statement, $driver_options );
	}

	/**
	 * Returns the last inserted id.
	 *
	 * @return string
	 */
	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}

	/**
	 * Calls a pdo method.
	 *
	 * @param string $method A method in the pdo object.
	 * @param array $arguments The array of arguments to pass to the method.
	 * @return mixed
	 */
	public function __call( $method, $arguments )
	{
		return call_user_func_array( array( $this->pdo, $method ), $arguments );
	}
}
