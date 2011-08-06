<?php
/**
 * Provides an interface to communicate with the Memcache server.
 *
 * @author Albert Garcia (albert.garcia AT obokaman DOT com)
 * @license BSD
 *
 * Usage:
 *
 * $m = MemcacheClient::getInstance(); // It will take automatically servers defined at memcached.config.php at active instance.
 *
 * $m->set( 'a', 'my value' ); // Stores in the key 'a' the value 'my value'
 *
 * This class is based on Memcache object implementation: http://www.php.net/manual/en/book.memcache.php
 *
 */

class MemcacheClient
{

	private static $instance = null;
	private static $memcache = null;

	/**
	 * Singleton to call from all other functions
	 */
	static function getInstance()
	{
		if ( self::$instance === null )
		{
			self::$instance = new MemcacheClient();
		}

		return self::$instance;
	}

	protected function __construct()
	{
		try
		{
			$servers = Config::getInstance()->getConfig( 'memcache', 'servers' );
		}
		catch ( Exception_Configuration $e )
		{
			// Default memcached address and listening port.
			$servers = array( array( '127.0.0.1' => 11211 ) );
		}

		self::$memcache = new Memcache();

		foreach ( $servers[0] as $server => $port )
		{
			self::$memcache->addServer( $server, $port );
		}
	}

	/**
	 * Check if Memcache is active and right connected
	 *
	 * @return integer
	 */
	static function isActive()
	{
		return (bool) @self::$memcache->getVersion();
	}

	/**
	 * Clear the cache
	 *
	 * @return void
	 */
	static function flush()
	{
		self::$memcache->flush();
	}

	/**
	 * Returns the value stored in the memory by it's key
	 *
	 * @param string $key
	 * @return mix
	 */
	static function get( $key )
	{
		return self::$memcache->get( $key );
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
	static function set( $key, $var, $compress=0, $expire=0 )
	{
		return self::$memcache->set( $key, $var, $compress?MEMCACHE_COMPRESSED:null, $expire );
	}

	/**
	 * Set the value in memcache if the value does not exist; returns FALSE if value exists
	 *
	 * @param sting $key
	 * @param mix $var
	 * @param bool $compress
	 * @param int $expire
	 * @return bool
	 */
	static function add( $key, $var, $compress=0, $expire=0 )
	{
		return self::$memcache->add( $key, $var, $compress?MEMCACHE_COMPRESSED:null, $expire );
	}

	/**
	 * Replace an existing value
	 *
	 * @param string $key
	 * @param mix $var
	 * @param bool $compress
	 * @param int $expire
	 * @return bool
	 */
	static function replace( $key, $var, $compress=0, $expire=0 )
	{
		return self::$memcache->replace( $key, $var, $compress?MEMCACHE_COMPRESSED:null, $expire );
	}

	/**
	 * Delete a record or set a timeout
	 *
	 * @param string $key
	 * @param int $timeout
	 * @return bool
	 */
	static function delete( $key, $timeout=0 )
	{
		return self::$memcache->delete( $key, $timeout );
	}

	/**
	 * Increment an existing integer value
	 *
	 * @param string $key
	 * @param mix $value
	 * @return bool
	 */
	static function increment( $key, $value=1 )
	{
		return self::$memcache->increment( $key, $value );
	}

	/**
	 * Decrement an existing value
	 *
	 * @param string $key
	 * @param mix $value
	 * @return bool
	 */
	static function decrement( $key, $value=1 )
	{
		return self::$memcache->decrement( $key, $value );
	}
}
?>