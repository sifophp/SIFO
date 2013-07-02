<?php
/**
 * LICENSE
 *
 * Copyright 2010 Alejandro Pérez <alexgt9@gmail.com>
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

use Exception;
use PDO;

/**
 * Handles the connection to the database
 */
class DatabaseConnection
{
	/**
	 * Instance of PDO.
	 * @var PDO
	 */
	static private $pdo = NULL;

	/**
	 * Class name of statement.
	 * @var string
	 */
	static private $statement_class = '\Sifo\DatabaseStatement';

	/**
	 * Association between driver and model.
	 * 
	 * @var array
	 */
	static private $driver_map = array(
		//'mysql' => '\\Sifo\\DatabaseMysql',
	);

	/**
	 * Instance name.
	 * @var string
	 */
	static private $instance = NULL;

	/**
	 * Force the execution in the master server.
	 * @var boolean
	 */
	static public $launch_in_master = false;

	/**
	 * Stores the current query type needed.
	 *
	 * @var integer
	 */
	static private $destination_type;

	/**
	 * Is the actual query read or write.
	 * @var string
	 */
	private $read_operation;

	/**
	 * Fetch for use in queries by default.
	 * @var integer
	 */
	public $default_fetch_mode = PDO::FETCH_ASSOC;

	/**
	 * Param of the actual connection.
	 *
	 * @var array
	 */
	static protected $active_connection;

	/**
	 * Identifies a query as write operation and is sent to the master.
	 *
	 * @var integer
	 */
	const TYPE_MASTER = 'master';

	/**
	 * Identifies a query as read operation and is sent to a slave.
	 *
	 * @var integer
	 */
	const TYPE_SLAVE = 'slave';

	/**
	 * No need to identify a query because is a single server.
	 *
	 * @var integer
	 */
	const TYPE_SINGLE_SERVER = 'single_server';

	/**
	 * Message of last error in the database.
	 * 
	 * @var string
	 */
	public $error;

	/**
	 * Singleton static method.
	 *
	 * @return DatabaseConnection
	 */
	static public function getInstance()
	{
		if ( self::$instance === null )
		{
			$driver = self::getDbDriver();
			if ( array_key_exists( $driver, self::$driver_map ) ) 
			{
				$class_name = self::$driver_map[$driver];
				self::$instance = new $class_name;
			}
			else
			{
				self::$instance = new self;
			}
		}

		return self::$instance;
	}

	/**
	 * Dummy
	 */
	private function __construct(){}

	/**
	 * Dummy
	 */
	private function __clone(){}

	/**
	 * Get the driver from the database params.
	 *
	 * @return string The name of the driver to use.
	 */
	static private function getDbDriver()
	{
		$db_params = Domains::getInstance()->getDatabaseParams();
		if ( isset( $db_params['profile'] ) )
		{
			$db_profiles = Config::getInstance()->getConfig( 'db_profiles', $db_params['profile'] );
			$db_params = $db_profiles['master'];
		}

		return $db_params['db_driver'];
	}

	/**
	 * Creates a DB object if necessary depending on the current operation requested.
	 * an action is triggered.
	 */
	public function connectDb()
	{
		if ( isset( self::$pdo[self::$destination_type] ) ) 
		{
			return;
		}

		$db_params = Domains::getInstance()->getDatabaseParams();

		if ( !is_array( self::$pdo ) )
		{
			if ( !isset( $db_params['profile'] ) )
			{
				// No Master/Slave schema expected:
				self::$destination_type = self::TYPE_SINGLE_SERVER;
			}
		}

		if ( !isset( self::$pdo[self::$destination_type] ) )
		{
			Benchmark::getInstance()->timingStart( 'db_connections' );

			try
			{
				if ( self::TYPE_SINGLE_SERVER == self::$destination_type )
				{
					$db_params = Domains::getInstance()->getDatabaseParams();
				}
				else // Instance uses MASTER/SLAVE schema:
				{
					$db_profiles = Config::getInstance()->getConfig( 'db_profiles', $db_params['profile'] );

					if ( self::$launch_in_master || self::TYPE_MASTER == self::$destination_type )
					{
						$db_params = $db_profiles['master'];
					}
					else
					{
						$lb = new LoadBalancerDatabase( $this );
						$lb->setNodes( $db_profiles['slaves'] );
						$selected_slave = $lb->get();
						$db_params = $db_profiles['slaves'][$selected_slave];
					}
				}
				self::$pdo[self::$destination_type] = $this->createConnection( $db_params );

				self::$pdo[self::$destination_type]->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, $this->default_fetch_mode );
				self::$pdo[self::$destination_type]->setAttribute( PDO::ATTR_STATEMENT_CLASS, array( self::$statement_class, array( self::$pdo[self::$destination_type], $this ) ) );

				self::$active_connection = $db_params;

				if ( isset( $db_params['db_init_commands'] ) && is_array( $db_params['db_init_commands'] ) )
				{
					foreach ( $db_params['db_init_commands'] as $command )
					{
						self::$pdo[self::$destination_type]->query( $command );
					}
				}
			}
				// If connection to database fails throw a SIFO 500 error.
			catch ( \PDOException $e )
			{
				throw new Exception_500( $e->getMessage(), $e->getCode() );
			}

			Benchmark::getInstance()->timingCurrentToRegistry( 'db_connections' );
		}
	}

	/**
	 * Specific treatment for create the connection.
	 *
	 * @param array $db_params The conection params.
	 */
	public function createConnection( $db_params )
	{
		return new PDO(
			"{$db_params['db_driver']}:host={$db_params['db_host']};dbname={$db_params['db_name']}",
			$db_params['db_user'],
			$db_params['db_password']
		);
	}

	/**
	 * Determinate the type of operation and the server who mus execute this statement.
	 * 
	 * Set the destination type in the static property.
	 *
	 * @param  string $statement The statement to execute.
	 */
	private function getDestination( $statement )
	{
		$this->read_operation = preg_match( '/^SELECT|^SHOW |^DESC /i', $statement );

		if ( self::$launch_in_master ) 
		{
			self::$destination_type = self::TYPE_MASTER;
			self::$launch_in_master = false;
		}
		else
		{
			// Query goes to a single server configuration? to a master? a slave?
			if ( self::TYPE_SINGLE_SERVER != self::$destination_type )
			{
				self::$destination_type = ( $this->read_operation ) ? self::TYPE_SLAVE : self::TYPE_MASTER;
			}
		}
	}

	/**
	 * Forces next query (only one) to be executed in the master.
	 */
	public function nextQueryInMaster()
	{
		return self::$launch_in_master = true;
	}

	/**
	 * Close database connection.
	 *
	 * @return void
	 */
	protected function closeConnectionDatabase()
	{
		// Unset current connection. In the next query execution it will reconnect automatically.
		unset( self::$pdo[self::$destination_type] );
	}

	/**
	 * Executes an SQL statement, returning a result set as a PDOStatement object.
	 * @param  string $query The query to execute.
	 * @param  string $tag   The identifier for the query.
	 * @return DatabaseStatement        The statement object.
	 */
	public function query( $query, $tag = null )
	{
		$statement = $this->prepare( $query, $tag );
		$statement->execute();

		return $statement;
	}

	/**
	 * Prepares a statement for execution and returns a statement object
	 * @param  string $query          The query to prepare.
	 * @param  string $tag            The identifier for the query.
	 * @param  array  $driver_options Additional driver options.
	 * @return DatabaseStatement                 The statement object.
	 */
	public function prepare( $query, $tag = null, $driver_options = array() )
	{
		$this->getDestination( $query );
		$this->connectDb();

		$statement = self::$pdo[self::$destination_type]->prepare( $query, $driver_options );
		$statement->setTag( $tag );

		return $statement;
	}

	/**
	 * Set Query Debug. It checks if dev mode is enabled and then stores debug data in registry.
	 *
	 * @param $answer
	 * @param $tag
	 * @param $method
	 * @param $read_operation
	 * @param $error
	 * @return void
	 */
	public function queryDebug( $answer, $query, $tag, $method, $error )
	{
		if ( !Domains::getInstance()->getDebugMode() )
		{
			return false;
		}

		$query_time = Benchmark::getInstance()->timingCurrentToRegistry( 'db_queries' );

		$debug_query = array(
			"tag"         => $tag ? : $this->getModelCaller(),
			"sql"         => $query,
			"type"        => ( $this->read_operation ? 'read' : 'write' ),
			"destination" => self::$destination_type,
			"host"        => self::$active_connection['db_host'],
			"database"    => self::$active_connection['db_name'],
			"user"        => self::$active_connection['db_user'],
			"controller"  => $this->getControllerCaller(),
			// Show a table with the method name and number (functions: Affected_Rows, Last_InsertID
			"resultset"   => $answer ? $answer->fetchAll() : $answer,
			"time"        => $query_time,
			"error"			=> $error,
			"duplicated"	=> false
		);

		if ( $debug_query['type'] == 'read' )
		{
			$debug_query['rows_num'] = $answer ? count( $answer->fetchAll() ) : 0;
		}
		else
		{
			$debug_query['rows_num'] = $answer ? $answer->rowCount() : 0;
		}

		// Check duplicated queries.
		$queries_executed = Debug::get( 'executed_queries' );
		if ( !empty( $queries_executed ) && isset( $queries_executed[ $debug_query['sql'] ] ) )
		{
			$debug_query['duplicated'] = true;
			Debug::push( 'duplicated_queries', 1 );
		}

		Debug::subSet( 'executed_queries', $debug_query['sql'], 1 );

		// Save query info in debug and add query errors if it's necessary.
		Debug::push( 'queries', $debug_query );
		if ( $error )
		{
			Debug::push( 'queries_errors', $error );
		}
	}

	/**
	 * Return the last insert id.
	 * 
	 * @return integer
	 */
	public function lastInsertId()
	{
		return self::$pdo[self::$destination_type]->lastInsertId();
	}

	/**
	 * Get the name of the controller that calls the model.
	 * @return string The name of the controller
	 */
	protected function getControllerCaller()
	{
		$back_trace = debug_backtrace();

		foreach ($back_trace as $track ) 
		{
			if ( strpos( $track['class'], 'Controller' ) ) 
			{
				return $track['class'];
			}
		}

		return "";
	}

	/**
	 * Return the tag if no one was provided.
	 *
	 * Get the class name of the model and the function used in it using backtrace.
	 *
	 * @return string The tag for use in the debug.
	 */
	protected function getModelCaller()
	{
		$back_trace = debug_backtrace();
		$model_parent_class = '\Sifo\DatabaseModel';
		$model_class = 'NOT FOUND';
		$method = 'NOT FOUND';

		foreach ($back_trace as $track ) 
		{
			if ( is_subclass_of( $track['class'], $model_parent_class ) ) 
			{
				$model_class = $track['class'];
				$method = $track['function'];
			}
		}
		
		$tag = 'Query from ' . $model_class . ' (' . $method . ')';

		return $tag;
	}


	/**
	 * Log mysql_errors to disk:
	 *
	 * @param $error
	 * @return void
	 */
	protected function writeDiskLog( $error )
	{
		$date = date( 'd-m-Y H:i:s' );
		$referer = FilterServer::getInstance()->getString( 'HTTP_REFERER' );
		$current_url = FilterServer::getInstance()->getString( 'SCRIPT_URI' );

		// Log mysql_errors to disk:
		$message = <<<MESSAGE
================================
Date: $date
URL: $current_url
Referer: $referer

Error code: {$error[0]}
Error code driver: {$error[1]}
Error: {$error[2]}\n
MESSAGE;

		file_put_contents( ROOT_PATH . '/logs/errors_database.log', $message, FILE_APPEND );
	}
}

/**
 * Load Balancer for database server choose.
 */
class LoadBalancerDatabase extends LoadBalancer
{
	/**
	 * Connection to database.
	 * 
	 * @var Connection
	 */
	private $connection;

	/**
	 * Initialize the connection.
	 * 
	 * @param Connection $connection The connection to the database.
	 */
	public function __construct( $connection )
	{
		$this->connection = $connection;
		parent::__construct();
	}

	/**
	 * Check if the server is working and add to the list of servers.
	 * @param integer $index           
	 * @param array $node_properties The properties of the connection.
	 */
	protected function addNodeIfAvailable( $index, $node_properties )
	{
		try
		{
			$this->connection->createConnection( $node_properties );

			// If no exception at this point the server is ready:
			$this->addServer( $index, $node_properties['weight'] );
		}
		catch ( \PDOException $e )
		{
			// The server is down, won't be added in the balancing. Log it:
			trigger_error( "SERVER IS DOWN! " . $node_properties['db_host'] );
		}

	}
}

/**
* Db_Exception
*/
class Db_Exception extends Exception{}

?>
