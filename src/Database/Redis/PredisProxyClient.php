<?php

namespace Sifo\Database\Redis;

use Sifo\Http\Domains;

include_once ROOT_PATH . '/vendor/predis/predis/src/Autoloader.php';


class PredisProxyClient
{
    static protected $instance;

    protected $client;
    protected $connection_params;

    public static function getInstance(Array $connection_params)
    {
        asort($connection_params);

        $key = md5(serialize($connection_params));
        if (isset(self::$instance[$key]))
        {
            return self::$instance[$key];
        }

        if (true !== Domains::getInstance()->getDebugMode())
        {
            self::$instance[$key] = new self($connection_params);
        }
        else
        {
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
        if (is_object($this->client))
        {
            return call_user_func_array(array($this->client, $method), $args);
        }

        return null;
    }
}
