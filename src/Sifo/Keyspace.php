<?php

namespace Sifo;

/**
 * A Keyspace is a structured definition on how you will store your keys
 * in a Key/Value storage system.
 *
 * You can use this class to access and set your Keys in systems like Redis.
 * @requires A configuration file keyspace.config.php.
 */
class Keyspace
{
    /**
     * Contains all the keyspace mapping taken from configuration.
     *
     * @var array
     */
    static protected $keyspace;

    /**
     * Returns the final Key name for the given parameters.
     *
     * @param string $key_name
     * @param array $parameters
     * @throws KeySpace_Exception
     */
    static public function get(
        $key_name,
        $parameters = null
    ) {
        if (!isset(self::$keyspace)) {
            self::$keyspace = \Sifo\Config::getInstance()->getConfig('keyspace');
        }

        if (!isset(self::$keyspace[$key_name])) {
            throw new KeySpace_Exception("Key named '$key_name' is not available in the key space.");
        }

        $key = self::$keyspace[$key_name];

        if (is_array($parameters)) {
            foreach ($parameters as $tag => $value) {
                $tag = preg_quote($tag, '/');
                $key = preg_replace("/<($tag)>/", $value, $key);
            }
        }

        // Remove any missing parameters:
        if (false !== strpos($key, '<')) {
            throw new KeySpace_Exception("The key contains undeclared parameters for replacement");
        }

        return $key;
    }
}

class KeySpace_Exception extends \Exception
{
}