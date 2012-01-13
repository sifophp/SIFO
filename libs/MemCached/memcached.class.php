<?php
namespace Sifo;

/**
 * Provides an interface to communicate with the Memcached server.
 *
 * $m = MemcachedClient::getInstance(); // It will take automatically servers defined at memcache.config.php at active instance.
 *
 * $m->set( 'a', 'my value' ); // Stores in the key 'a' the value 'my value'
 *
 * This class is based on Memcached object implementation: http://www.php.net/manual/es/book.memcached.php
 *
 */

class MemcachedClient extends \Memcached
{

	private static $instance = null;

	/**
	 * Singleton to call from all other functions
	 */
	static function getInstance()
	{
		if ( self::$instance === null )
		{
			self::$instance = new MemcachedClient();
		}

		return self::$instance;
	}

	public function __construct()
	{
		parent::__construct();
		try
		{
			$servers = Config::getInstance()->getConfig( 'memcache', 'servers' );
		}
		catch ( Exception_Configuration $e )
		{
			// Default memcached address and listening port.
			$servers = array( array( '127.0.0.1' => 11211 ) );
		}

		foreach ( $servers[0] as $server => $port )
		{
			$this->addServer( $server, $port );
		}
	}

	/**
	 * Check if Memcache is active and right connected
	 *
	 * @return integer
	 */
	public function isActive()
	{
		return (bool) @$this->getVersion();
	}

	/**
	 * Store the value in the memcache memory (overwrite if key exists)
	 *
	 * @param string $key
	 * @param mix $var
	 * @param bool $compress
	 * @param int $expire (seconds before item expires)
	 * @return bool
	 */
	public function set( $key, $var, $compress=0, $expire=0 )
	{
		return parent::set( $key, $var, $expire );
	}
}
?>