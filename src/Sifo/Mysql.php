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

use PDO,PDOStatement;
/**
 * DbStatement class that is extended to customize some PDO functionality.
 */
class MysqlStatement extends PDOStatement
{
	/**
	 * The pdo object instance.
	 *
	 * @var PDO Object.
	 */
	public $dbh;

	/**
	 * The domains.config params related to the database.
	 *
	 * @var array
	 */
	protected $db_params;

	/**
	 * Construction method. Sets the pdo object and the db parameters.
	 *
	 * @param PDO $dbh The pdo instance executing the statement.
	 * @param string $profile The profile being used for this statement.
	 */
    protected function __construct( $dbh, $profile )
	{
        $this->dbh = $dbh;
		$params = Domains::getInstance()->getDatabaseParams();

		if (!array_key_exists($profile, $params))
		{
			$params[$profile] = $params;
		}

		$this->db_params = $params[$profile];
    }

	/**
	 * Executes the current statement.
	 *
	 * @param array $parameters The array of parameters to be replaced in the statement.
	 * @param string $context The context of the query.
	 * @return bool True if everything went OK, false otherwise.
	 */
	public function execute( $parameters = array(), $context = null )
	{
		if ( $parameters !== array() )
		{
			return parent::execute( $parameters );
		}
		else
		{
			return parent::execute();
		}
	}

	/**
	 * Fetches the resultset. Extended to make PDO::FETCH_ASSOC as default $fetch_style.
	 *
	 * @param integer $fetch_style Controls how the next row will be returned to the caller. This value must be one of the PDO::FETCH_* constants, defaulting to PDO::FETCH_ASSOC.
	 * @param integer $cursor_orientation For a PDOStatement object representing a scrollable cursor, this value determines which row will be returned to the caller. This value must be one of the PDO::FETCH_ORI_* constants, defaulting to PDO::FETCH_ORI_NEXT. To request a scrollable cursor for your PDOStatement object, you must set the PDO::ATTR_CURSOR attribute to PDO::CURSOR_SCROLL when you prepare the SQL statement with PDO::prepare().
	 * @param integer $cursor_offset For a PDOStatement object representing a scrollable cursor for which the cursor_orientation parameter is set to PDO::FETCH_ORI_ABS, this value specifies the absolute number of the row in the result set that shall be fetched.
	 * @return mixed
	 */
	public function fetch( $fetch_style = PDO::FETCH_ASSOC, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0 )
	{
		return parent::fetch( $fetch_style, $cursor_orientation, $cursor_offset );
	}

	/**
	 * Returns an array containing all of the result set rows.
	 *
	 * @param integer $fetch_style Controls the contents of the returned array as documented in PDOStatement::fetch().
	 * @param mixed $fetch_argument This argument have a different meaning depending on the value of the fetch_style parameter.
	 * @param array $ctor_args Arguments of custom class constructor when the fetch_style parameter is PDO::FETCH_CLASS.
	 * @return array
	 */
	public function fetchAll( $fetch_style = PDO::FETCH_ASSOC, $fetch_argument = null, $ctor_args = array() )
	{
		if ( $fetch_argument === null )
		{
			return parent::fetchAll( $fetch_style );
		}

		return parent::fetchAll( $fetch_style, $fetch_argument, $ctor_args );
	}
}

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
	public function query( $statement, $context = null )
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