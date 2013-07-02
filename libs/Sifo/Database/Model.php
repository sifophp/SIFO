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
		$this->init();
		$this->database = DatabaseConnection::getInstance();
	}

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
}

?>
