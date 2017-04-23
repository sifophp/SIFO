<?php

namespace Sifo\Cache;

/**
 * Wrapper for the PECL Memcache extension.
 *
 * @see http://www.php.net/manual/en/memcache.installation.php
 */
class Memcache extends Base
{
    protected $cache_object = null;

    /**
     * Returns an instance of the Memcache object with the configured servers.
     */
    public function __construct()
    {
        try {
            $servers = \Sifo\Config::getInstance()->getConfig('cache', 'servers');
        } catch (\Sifo\Exception\ConfigurationException $e) {
            // Default memcached address and listening port.
            $servers = array(array('127.0.0.1' => 11211));
        }

        $this->cache_object = new \Sifo\Cache\MemcacheAdapter();

        foreach ($servers[0] as $server => $port) {
            $this->cache_object->addServer($server, $port);
        }
    }
}
