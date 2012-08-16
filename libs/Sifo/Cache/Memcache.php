<?php
/**
 * Provides an interface to communicate with the Memcache server.
 *
 * @author Albert Garcia (albert.garcia AT obokaman DOT com)
 * @license BSD
 *
 * Usage:
 *
 * $m = CacheMemcache::getInstance(); // It will take automatically servers defined at memcached.config.php at active instance.
 *
 * $m->set( 'a', 'my value' ); // Stores in the key 'a' the value 'my value'
 *
 * This class is based on Memcache object implementation: http://www.php.net/manual/en/book.memcache.php
 *
 */
namespace Sifo;
use \Memcache;

/**
 * Wrapper for the PECL Memcache extension.
 *
 * @see http://www.php.net/manual/en/memcache.installation.php
 */
class CacheMemcache extends CacheBase
{

	private static $instance = null;
	protected $cache_object = null;

	/**
	 * Returns an instance of the Memcache object with the configured servers.
	 *
	 * @return Memcache
	 */
	public function __construct()
	{
		try
		{
			$servers = \Sifo\Config::getInstance()->getConfig( 'cache', 'servers' );
		}
		catch ( \Sifo\Exception_Configuration $e )
		{
			// Default memcached address and listening port.
			$servers = array( array( '127.0.0.1' => 11211 ) );
		}

		$this->cache_object = new Memcache;

		foreach ( $servers[0] as $server => $port )
		{
			$this->cache_object->addServer( $server, $port );
		}

		return $this->cache_object;
	}

	/**
	 * Stores the given "content" under the key "$key" on the memcached server.
	 *
	 * Parameter expire is expiration time in seconds. If it's 0, the item never expires (but memcached server doesn't
	 * guarantee this item to be stored all the time, it could be deleted from the cache to make place for other items)
	 *
	 * The compression is removed from the parameters to make it compatible with the rest of caching systems.
	 *
	 * @param string $key
	 * @param mixed $content
	 * @param integer $expire Timestamp or number of seconds until expiration. If passed in seconds value over 30 days is not understood.
	 *
	 * @return boolean True on success or false on failure.
	 */
	public function set( $key, $content, $expire = 0 )
	{
		// Compression parameter is not needed in the framework implementation, also it does not work well with small values.
		$compress = 0; // or MEMCACHE_COMPRESSED for compression.

		return $this->cache_object->set( $key, $content, $compress, $expire );
	}

}