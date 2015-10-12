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
 */
namespace Sifo\Cache;

use Sifo\Exception\ConfigurationException;

/**
 * Wrapper for the PECL Memcache extension.
 *
 * @see http://www.php.net/manual/en/memcache.installation.php
 */
class Memcache extends Base
{
    /** @var \Memcache */
    protected $cache_object;

    /**
     * Returns an instance of the \Memcache object with the configured servers.
     */
    public function __construct()
    {
        try {
            $servers = \Sifo\Config::getInstance()->getConfig('cache', 'servers');
        } catch (ConfigurationException $e) {
            // Default memcached address and listening port.
            $servers = array(array('127.0.0.1' => 11211));
        }

        $this->cache_object = new MemcacheAdapter();

        foreach ($servers[0] as $server => $port) {
            $this->cache_object->addServer($server, $port);
        }

        return $this->cache_object;
    }
}
