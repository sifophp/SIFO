<?php

namespace Sifo;

use Sifo\Exception\RegistryException;

/**
 * Global storage of objects in memory. They expire after script execution.
 */
class Registry
{
    /**
     * Registry object provides storage for shared objects.
     */
    private static $instance;

    /**
     * Array where all the storage is done.
     *
     * @var array
     */
    private static $storage = [];

    /**
     * Retrieves the default registry instance.
     *
     * @return Registry
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Avoid external construction of class without singleton usage.
     *
     */
    private function __construct()
    {
    }

    /**
     * Get a value from the registry.
     *
     * @param string $key Name you used to store the value.
     *
     * @throws RegistryException
     * @return mixed
     */
    public static function get(string $key)
    {
        if (self::keyExists($key)) {
            return self::$storage[$key];
        }

        /** @deprecated Registry shouldn't allow getting a non-existent key. Soon this will raise an Exception */
        @trigger_error('Registry doesn\'t contain any element named "' . $key . '". Soon this will raise an Exception. Please, use keyExists instead if you want to verify its existence.',
            E_USER_WARNING);
    }

    /**
     * Stores the object with the name given in $key.
     *
     * @param string $key Name you want to store the value with.
     * @param mixed $value The object to store in the array.
     *
     * @return void
     */
    public static function set(string $key, $value)
    {
        self::$storage[$key] = $value;
    }

    /**
     * Unset the object with the name given in $key.
     *
     * @param string $key Name you want to store the value with.
     *
     * @return void
     */
    public static function invalidate(string $key)
    {
        if (isset(self::$storage[$key])) {
            unset(self::$storage[$key]);
        }
    }

    /**
     * Adds another element to the end of the array.
     *
     * @param string $key
     * @param mixed $value
     *
     * @throws RegistryException
     * @return void
     */
    public static function push(string $key, $value)
    {
        if (!self::keyExists($key)) {
            self::$storage[$key] = [];
        }

        if (!is_array(self::$storage[$key])) {
            throw new RegistryException('Failed to PUSH an element in the registry because the given key "' . $key . '" is not an array.');
        }

        array_push(self::$storage[$key], $value);
    }

    /**
     * @param $key
     * @return bool
     */
    public static function keyExists($key): bool
    {
        return array_key_exists($key, self::$storage);
    }
}
