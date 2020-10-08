<?php

namespace Sifo;

use DateTime;
use PDO;
use PDOException;
use function print_r;
use function var_export;

class DebugDataBaseHandler
{

	/**
	 * @var string Path to store the Sifo database (set in the class constructor in order to build it based on the ROOT_PATH).
	 */
	private $db_path;

	/**
	 * @var string Sifo database name
	 */
	private $db_name = 'sifo.sqlite3';

	/**
	 * @var string table to store execution debugs name
	 */
	private $table_name = 'execution_debugs';

	/**
	 * @var int We'll keep the executions of the last $days_to_keep_debugs days in the database
	 */
	private $days_to_keep_debugs = 1;

	/**
	 * Connects to the Sifo database and initializes the debug table if it doesn't exists
	 */
	function __construct()
	{
		$this->db_path = ROOT_PATH . '/logs/';

		try
		{
			$this->persistence = new PDO( 'sqlite:' . $this->db_path . $this->db_name );

			$this->persistence->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); // Set database errors mode to exceptions
		}
		catch ( PDOException $e )
		{
			trigger_error( "[sifo] [debug] Could not connect to the debug database using the following DSN: 'sqlite:" . $this->db_path . $this->db_name . "'.\n
			Error message: " . $e->getMessage() );
		}

		$this->createSifoDebugDbTable();
	}

	/**
	 * Initializes the Sifo debug database table
	 */
	private function createSifoDebugDbTable()
	{
		try
		{
			$this->persistence->exec( "CREATE TABLE IF NOT EXISTS $this->table_name (
	                    execution_key TEXT PRIMARY KEY,
	                    url TEXT,
	                    debug_content TEXT,
	                    is_json INTEGER,
	                    is_pinned INTEGER,
	                    timestamp INTEGER,
	                    parent_execution_key TEXT)" );

			$this->persistence->exec( "CREATE INDEX IF NOT EXISTS timestamp_index ON $this->table_name( timestamp, is_pinned )" );
		}
		catch ( PDOException $e )
		{
			trigger_error( "[sifo] [debug] Could not create the Sifo debug database.\n
			Error message: " . $e->getMessage() . "\n
			Error info: " . var_export($this->persistence->errorInfo()) );
		}
	}

	/**
	 * Saves an execution debug on the Sifo debug database.
	 *
	 * @param string $execution_key unique identifier of the execution to store.
	 * @param string $url
	 * @param array  $debug_content containing all the debug data and its debug modules.
	 * @param bool   $is_json       indicating if this execution is returned as a JSON or not.
	 */
	public function saveExecutionDebug( $execution_key, $url, $debug_content, $is_json )
	{
		try
		{
			$insert    = "INSERT OR REPLACE INTO $this->table_name (execution_key, url, debug_content, is_json, is_pinned, timestamp)
                VALUES ( :execution_key, :url, :debug_content, :is_json, 0, :timestamp )";
			$statement = $this->persistence->prepare( $insert );

			$date_time = new DateTime();
            $timestamp = $date_time->getTimestamp();
			$is_json   = (int) $is_json;

			$statement->bindParam( ':execution_key', $execution_key, PDO::PARAM_STR );
			$statement->bindParam( ':url', $url, PDO::PARAM_STR );
            $encoded_debug = json_encode( $debug_content );
			$statement->bindParam( ':debug_content', $encoded_debug , PDO::PARAM_STR );
			$statement->bindParam( ':is_json', $is_json, PDO::PARAM_INT );
			$statement->bindParam( ':timestamp', $timestamp, PDO::PARAM_INT );

			$statement->execute();
		}
		catch ( PDOException $e )
		{
			trigger_error( "[sifo] [debug] Could not insert the execution debug record in the Sifo debug database.\n
			Error message: " . $e->getMessage() . "\n
			Error info: " . var_export($this->persistence->errorInfo()) );
		}
	}

	/**
	 * Deletes execution debugs older than $this->days_to_keep_debugs days.
	 * Do not delete the pinned execution debugs.
	 */
	public function cleanOldExecutionDebugs()
	{
		try
		{
			$delete    = "DELETE FROM $this->table_name WHERE timestamp <= :timestamp AND is_pinned = 0";
			$statement = $this->persistence->prepare( $delete );

			$date_time_to_delete = new DateTime();
			$date_time_to_delete->sub( new \DateInterval( 'P' . $this->days_to_keep_debugs . 'D' ) );
            $timestamp = $date_time_to_delete->getTimestamp();

			$statement->bindParam( ':timestamp', $timestamp, PDO::PARAM_INT );

			$statement->execute();
		}
		catch ( PDOException $e )
		{
			trigger_error( "[sifo] [debug] Could not delete the old execution debugs records from the Sifo debug database.\n
			Error message: " . $e->getMessage() . "\n
			Error info: " . var_export( $this->persistence->errorInfo()));
		}
	}

	/**
	 * Returns all the execution debug data based on an ID.
	 *
	 * @param string $execution_key Execution debug identifier.
	 *
	 * @return bool|mixed false if we couldn't execute the prepared statement. All execution debug data in an associative array otherwise.
	 */
	public function getExecutionDebugWithChildrenById( $execution_key )
	{
		try
		{
			$query     = "SELECT execution_key, url, debug_content, is_json, is_pinned, timestamp, parent_execution_key FROM $this->table_name WHERE execution_key = :execution_key OR parent_execution_key = :execution_key";
			$statement = $this->persistence->prepare( $query );

			$statement->bindParam( ':execution_key', $execution_key, PDO::PARAM_STR );

			if ( $statement->execute() )
			{
				// Structure in the $all_executions_debug_data the parent execution data and an array of children executions depending
				// on if the execution_key is the received one or not.
				$all_executions_debug_data = array();

				foreach ( $statement->fetchAll( PDO::FETCH_ASSOC ) as $execution_debug_data )
				{
					if ( $execution_debug_data['execution_key'] == $execution_key )
					{
						$all_executions_debug_data['parent_execution'] = $this->unmapExecutionDebugData( $execution_debug_data );
					}
					else
					{
						$all_executions_debug_data['children_executions'][$execution_debug_data['execution_key']] = $this->unmapExecutionDebugData( $execution_debug_data );
					}
				}

				return $all_executions_debug_data;
			}
			else
			{
				return false;
			}
		}
		catch ( PDOException $e )
		{
			trigger_error( "[sifo] [debug] Could not get the execution debug content from the Sifo debug database for the execution ID: $execution_key.\n
			Error message: " . $e->getMessage() . "\n
			Error info: " . var_export($this->persistence->errorInfo()) );

			return false;
		}
	}

	/**
	 * Unmap the data stored in the database to the format expected by the controller in order to deal with it.
	 *
	 * @param array $execution_debug_data with all the execution debug raw data retrieved from the data base as it was stored.
	 *
	 * @return array of saved execution debug data formatted properly in order to bring it to the controller.
	 */
	private function unmapExecutionDebugData( $execution_debug_data )
	{
		$execution_debug_data['debug_content'] = json_decode( $execution_debug_data['debug_content'], true );
		$execution_debug_data['date_time']     = new DateTime( '@' . $execution_debug_data['timestamp'] );
		$execution_debug_data['date_time']     = $execution_debug_data['date_time']->format( 'Y-m-d H:i:s' );

		return $execution_debug_data;
	}

	/**
	 * Returns the last execution_key inserted in the sifo debug table without taking into account the children executions.
	 *
	 * @param bool $is_json filter by json executions or not:
	 *                      false: return only non json executions (default)
	 *                      true: return only json executions
	 *                      null: return the last execution without taking into account if it's json or not
	 *
	 * @return bool|string depending on if we could execute the prepared statement or not.
	 */
	public function getLastParentExecutionKey( $is_json = null )
	{
		try
		{
			$query = "SELECT execution_key FROM $this->table_name WHERE parent_execution_key IS NULL";

			if ( isset( $is_json ) )
			{
				$query .= " AND is_json = " . (int)$is_json;
			}

			$query .= " ORDER BY timestamp DESC LIMIT 1";
			$statement = $this->persistence->prepare( $query );

			if ( $statement->execute() )
			{
				$execution_debug_data = $statement->fetch( PDO::FETCH_ASSOC );

				return $execution_debug_data['execution_key'];
			}
			else
			{
				return false;
			}
		}
		catch ( PDOException $e )
		{
			trigger_error( "[sifo] [debug] Could not get the last execution key from the Sifo debug database.\n
			Error message: " . $e->getMessage() . "\n
			Error info: " . var_export($this->persistence->errorInfo()) );

			return false;
		}
	}

	public function linkChildExecutionToParent( $child_execution_key, $parent_execution_key )
	{
		try
		{
			$query     = "UPDATE $this->table_name SET parent_execution_key = :parent_execution_key WHERE execution_key = :child_execution_key";
			$statement = $this->persistence->prepare( $query );

			$statement->bindParam( ':parent_execution_key', $parent_execution_key, PDO::PARAM_STR );
			$statement->bindParam( ':child_execution_key', $child_execution_key, PDO::PARAM_STR );

			return $statement->execute();
		}
		catch ( PDOException $e )
		{
			trigger_error( "[sifo] [debug] Could not link a child execution to its parent. child_execution_key: $child_execution_key, parent_execution_key: $parent_execution_key.\n
			Error message: " . $e->getMessage() . "\n
			Error info: " . var_export($this->persistence->errorInfo()) );

			return false;
		}
	}

	public function pinExecution( $execution_key, $is_pinned )
	{
		try
		{
			$query     = "UPDATE $this->table_name SET is_pinned = :is_pinned WHERE execution_key = :execution_key OR parent_execution_key = :execution_key";
			$statement = $this->persistence->prepare( $query );

			$statement->bindParam( ':execution_key', $execution_key, PDO::PARAM_STR );
			$statement->bindParam( ':is_pinned', $is_pinned, PDO::PARAM_INT );

			return $statement->execute();
		}
		catch ( PDOException $e )
		{
			trigger_error( "[sifo] [debug] Could not pin a execution. execution_key: $execution_key, is_pinned: $is_pinned.\n
			Error message: " . $e->getMessage() . "\n
			Error info: " . var_export($this->persistence->errorInfo()) );

			return false;
		}
	}
}
