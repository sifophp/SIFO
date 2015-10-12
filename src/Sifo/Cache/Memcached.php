<?php

/**
 * LICENSE.
 *
 * Copyright 2010 Albert Lombarte
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *	 http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Sifo\Cache;

use Sifo\Config;
use Sifo\Exception\ConfigurationException;

/**
 * Provides an interface to communicate with the Memcached server.
 *
 * This class is based on Memcached PHP's object implementation: http://www.php.net/manual/es/book.memcached.php
 */
class Memcached extends Base
{
    /**
     * Returns an instance of the Memcached object with the configured servers.
     *
     * @return Memcached
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

        return $this->cache_object;
    }
}
