<?php

namespace Sifo\Cache;

use Sifo\Config;
use Sifo\Exception\ConfigurationException;

/**
 * Provides an interface to communicate with the Memcached server.
 *
 * This class is based on Memcached PHP's object implementation: http://www.php.net/manual/es/book.memcached.php
 *
 */
class Memcached extends Base
{
    /**
     * Returns an instance of the Memcached object with the configured servers.
     */
    public function __construct()
    {
        $this->cache_object = new \Memcached();

        try {
            $servers = Config::getInstance()->getConfig('cache', 'servers');
        } catch (ConfigurationException $e) {
            // Default memcached address and listening port.
            $servers = array(array('127.0.0.1' => 11211));
        }

        foreach ($servers[0] as $server => $port) {
            $this->cache_object->addServer($server, $port);
        }
    }

    public function isActive()
    {
        return (false != $this->cache_object->getVersion());
    }
}
