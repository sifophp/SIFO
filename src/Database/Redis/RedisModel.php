<?php

namespace Sifo\Database\Redis;

use Sifo\Config;
use Sifo\Exception\ConfigurationException;
use Sifo\Http\Domains;

/**
 * Predis adapter for Sifo.
 */
class RedisModel
{
    /**
     * Redis client object.
     *
     * @var \Predis\Client
     */
    private static $connected_client = array();

    private $profile;

    /**
     * Connect to redis and return a redis object to start passing commands.
     *
     * If no profile is passed, default connection stated in domains.config.php is taken. Otherwise, profile
     * will be searched in redis.config.php.
     *
     * @param string $profile Connection profile.
     *
     * @return \Predis\Client
     */
    public function connect($profile = null)
    {
        if (!isset(self::$connected_client[$profile])) {
            \Predis\Autoloader::register(true);

            if (null == $profile) {
                try {
                    // Load "default" profile from redis.config.php:
                    $db_params = Config::getInstance()->getConfig('redis', 'default');
                } catch (ConfigurationException $e) {
                    // Connection taken from domains.config.php:
                    $db_params = Domains::getInstance()->getParam('redis');
                }
            } else // Advanced configuration taken from redis.config.php
            {
                $db_params = Config::getInstance()->getConfig('redis', $profile);
            }

            self::$connected_client[$profile] = PredisProxyClient::getInstance($db_params);
            $this->profile = $profile;
        }

        return self::$connected_client[$profile];
    }

    /**
     * Disconnect from server and reset the static object for reconnection.
     */
    public function disconnect()
    {
        self::$connected_client[$this->profile]->disconnect();
        self::$connected_client[$this->profile] = null;
    }

    /**
     * Disconnect clients on object destruction.
     */
    public function __destruct()
    {
        foreach (self::$connected_client as $client) {
            $client->disconnect();
        }
    }
}
