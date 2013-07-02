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

use PDO;
use PDOStatement;

/**
 * Stamenent returned by the database.
 */
class DatabaseStatement extends PDOStatement
{
	/**
	 * Reference to the database object.
	 * 
	 * @var DatabaseConnection.
	 */
	protected $database;

	/**
	 * The pdo object instance.
	 *
	 * @var PDO Object.
	 */
	public $dbh;

	/**
	 * String for of PDO data types.
	 * 
	 * @var array
	 */
	protected $data_types = array(
		's' => PDO::PARAM_STR,
		'i' => PDO::PARAM_INT,
		'b' => PDO::PARAM_BOOL,
		'n' => PDO::PARAM_NULL,
	);

	/**
	 * Result set of fetch all, to reuse.
	 * 
	 * Necesary for fetch result in debug before.
	 * 
	 * @var array
	 */
	protected $result_set;

	/**
	 * Construction method. Sets the pdo object and the db parameters.
	 *
	 * @param PDO $dbh The pdo instance executing the statement.
	 */
    protected function __construct( $dbh, $database )
	{
        $this->dbh = $dbh;
        $this->database = $database;
    }

    /**
     * Set the tag of the actual query.
     * 
     * @param string $tag The tag of the actual query.
     */
    public function setTag( $tag )
    {
    	$this->tag = $tag;
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
		if ( !$this->result_set )
		{
			if ( $fetch_argument === null )
			{
				$this->result_set = parent::fetchAll( $fetch_style );
			}
			else
			{
				$this->result_set = parent::fetchAll( $fetch_style, $fetch_argument, $ctor_args );
			}
		}

		return $this->result_set;
	}

	/**
	 * Execute the prepared statement
	 * @param  array  $parameters Parameters to bind
	 * @return array|boolean             The result of the query or false if failure.
	 */
	public function execute( $parameters = array() )
	{
		Benchmark::getInstance()->timingStart( 'db_queries' );

		if ( $parameters ) 
		{
			$answer = parent::execute( $parameters );
		}
		else
		{
			$answer = parent::execute();
		}
		
		$error = $this->errorInfo();

		$this->database->queryDebug( $this, $this->queryString, $this->tag, 'query', $error[0] != 0 ? $error[2] : false );
		
		if ( $error[0] != 0 ) 
		{
			$this->writeDiskLog( $error );
			$this->error = $error[2];
			return false;
		}

		return $this->fetchAll();
	}

	/**
	 * Binds a parameter to the specified variable name
	 *
	 * Binds a PHP variable to a corresponding named or question mark placeholder in the SQL statement
	 * that was used to prepare the statement. 
	 * 
	 * Unlike PDOStatement::bindValue(), the variable is bound as a reference and will 
	 * only be evaluated at the time that PDOStatement::execute() is called.
	 * 
	 * @param  mixed $parameter The name on the form :name or de 1-index for question marks.
	 * @param  mixed $variable  Name of the PHP variable to bind to the SQL statement parameter.
	 * @param  string $data_type The type of data, s for string, i for integer, b for boolean, n for null.
	 * @return boolean            True on success, false on failure.
	 */
	public function bindParam( $parameter, &$variable, $data_type = 's' )
	{
		return parent::bindParam( $parameter, $variable, $this->data_types[$data_type] );
	}

	/**
	 * Returns the number of rows affected by the last SQL statement.
	 * 
	 * @return integer The number of affected rows.
	 */
	public function affectedRows()
	{
		return $this->rowCount();
	}

	/**
	 * Return the last insert id.
	 * 
	 * @return integer
	 */
	public function lastInsertId()
	{
		return $this->database->lastInsertId();
	}
}