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

namespace Sifo\Debug;

use Sifo\Benchmark;
use Sifo\Mysql\Mysql;

/**
 * Database debug class. Extends the parent with benchmarking and debug utilities.
 *
 * This is done in a separate class to avoid decreased performance in production environments.
 */
class DebugMysql extends Mysql
{
	/**
	 * The singleton instance of this class.
	 *
	 * @var Db Object.
	 */
	static private $instance = NULL;

	/**
	 * The statement class that will be used.
	 *
	 * @var string
	 */
	const STATEMENT_CLASS = '\\Sifo\\Debug\\DebugMysqlStatement';

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

			self::$instance[$profile] = new DebugMysql( $profile );

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
		Benchmark::getInstance()->timingStart( 'db_queries' );

		$result = $this->pdo->query( $statement );

		$query_time = Benchmark::getInstance()->timingCurrentToRegistry( 'db_queries' );

		$this->setDebug( $statement, $query_time, $context, $result, $this->db_params, $this->pdo );

		return $result;
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
		Benchmark::getInstance()->timingStart( 'db_' . $method );

		$result = call_user_func_array( array( $this->pdo, $method ), $arguments );

		$query_time = Benchmark::getInstance()->timingCurrentToRegistry( 'db_' . $method );

		if ( $arguments !== array() )
		{
			DebugMysql::setDebug( $arguments[0], $query_time, $arguments[1], $result, $this->db_params );
		}

		return $result;
	}

	/**
	 * Fills some debug data to be displayed in the debug interface.
	 *
	 * @param string $statement The sql statement being queried.
	 * @param float $query_time The time that the query needed to be completed.
	 * @param string $context The context of the sql query.
	 * @param integer|array $resultset The result of the query.
	 */
	public static function setDebug( $statement, $query_time, $context, $resultset, $db_params, $pdo = null )
	{
		if ( $resultset !== false )
		{
			$error = $resultset->errorInfo();
			$resultset_array = $resultset->fetchAll();
			$rows_num = $resultset->rowCount();
		}
		else
		{
			$error = $pdo->errorInfo();
			$resultset_array = 0;
			$rows_num = 0;
		}

		$debug_query = array(
			"tag" => $context,
			"sql" => $statement,
			"type" => ( ( 0 === stripos( $statement, 'SELECT' ) ) ? 'read' : 'write' ),
			"host" => $db_params['db_host'],
			"database" => $db_params['db_name'],
			"user" => $db_params['db_user'],
			"trace" => DebugMysql::generateTrace( debug_backtrace( false ) ),
			// Show a table with the method name and number (functions: Affected_Rows, Last_InsertID
			"resultset" => $resultset_array,
			"time" => $query_time,
			"error" => ( isset( $error[2] ) !== false ) ? $error[2] : false
		);

		$debug_query['rows_num'] = $rows_num;

		if ( $debug_query['error'] !== false )
		{
			// Log mysql_errors to disk:
			file_put_contents( ROOT_PATH . '/logs/errors_database.log', "================================\nDate: " . date( 'd-m-Y H:i:s') . "\nError:\n". $error . "\n ", FILE_APPEND );
			Debug::push( 'queries_errors', $error );
		}

		Debug::push( 'queries', $debug_query );
	}

	/**
	 * Generates a trace to know where the query was executed.
	 *
	 * @return string
	 */
	public static function generateTrace( $debug_backtrace )
	{
		array_shift( $debug_backtrace );

		$trace = '';
		foreach ( $debug_backtrace as $key => $step )
		{
			$trace .= "#$key {$step['file']}({$step['line']}) : {$step['class']}::{$step['function']}()\n";
		}

		return $trace;
	}
}