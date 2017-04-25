<?php

namespace Sifo\Cache;

use Sifo\Config;
use Sifo\Exception\ConfigurationException;

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
            $servers = Config::getInstance()->getConfig('cache', 'servers');
        } catch (ConfigurationException $e) {
            $servers = array(array('127.0.0.1' => 11211));
        }

        $this->cache_object = new MemcacheAdapter();

        foreach ($servers[0] as $server => $port) {
            $this->cache_object->addServer($server, $port);
        }
    }

    public function isActive()
    {
        return (false != $this->cache_object->getVersion());
    }
}
