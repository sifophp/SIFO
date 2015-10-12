<?php
/**
 * LICENSE
 *
 * Copyright 2010 Albert Lombarte
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Sifo\Redis;

use Sifo\Domains;

class PredisProxyClient
{
    protected static $instance;

    protected $client;
    protected $connection_params;

    public static function getInstance(Array $connection_params)
    {
        asort($connection_params);

        $key = md5(serialize($connection_params));
        if (isset(self::$instance[$key])) {
            return self::$instance[$key];
        }

        if (true !== Domains::getInstance()->getDebugMode()) {
            self::$instance[$key] = new self($connection_params);
        } else {
            self::$instance[$key] = new PredisProxyClient($connection_params);
        }

        return self::$instance[$key];
    }

    protected function __construct(Array $connection_params)
    {
        $this->connection_params = $connection_params;
        $this->client            = new \Predis\Client($connection_params);
    }

    public function __call($method, $args)
    {
        if (is_object($this->client)) {
            return call_user_func_array(array($this->client, $method), $args);
        }

        return null;
    }
}
