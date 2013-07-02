<?php
/**
 * @author Alejandro Pérez <alexgt9@gmail.com>
 *
 */

namespace Sifo;

require_once ROOT_PATH . '/libs/adodb5/adodb.inc.php';

use PDO;

/**
 * Handles the connection to the database
 */
class DatabaseModel
{
	/**
	 * Instance of the database.
	 * 
	 * @var DatabaseConnection
	 */
	protected $database;

	/**
	 * Initialize the database instance.
	 */
	public function __construct()
	{
		$this->database = DatabaseConnection::getInstance();
	}

	/**
	 * Delegate all call to the database object.
	 * @param  string $method    The method called.
	 * @param  array $arguments The arguments passed.
	 * @return mixed
	 */
	public function __call( $method, $arguments )
	{
		return call_user_func_array( array( $this->database, $method ) , $arguments );
	}
}

?>
