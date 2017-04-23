<?php

namespace Sifo\Cache;

class Lock
{
    /** Maximum time a lock is effective. */
    const TTL = 8;

    /** Time until the next locking check is performed (in microseconds). */
    const WAIT_TIME = 100000; // 0,1 seconds

    /** Cache key prefix. */
    const KEY_PREFIX = '$LOCK$';

    /** @var string */
    protected $lock_id;

    /** @var string */
    protected $key;

    /** @var Lock[] */
    private static $instances;

    /** @var Base */
    protected $cache_object;

    private function __construct($key, $cache_instance)
    {
        $this->lock_id = uniqid();
        $this->key = $key;
        $this->cache_object = $cache_instance;
    }

    /**
     * @param string $original_key
     * @param Base $cache_instance
     *
     * @return Lock
     */
    public static function getInstance($original_key, $cache_instance)
    {
        $key = self::KEY_PREFIX . $original_key;

        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self($key, $cache_instance);
        }

        return self::$instances[$key];
    }

    /**
     * Returns if another cache calculation is in progress.
     *
     * @return boolean
     */
    public function isLocked()
    {
        // The flow is not locked if the current process is the lock holder.
        return (($value = $this->cache_object->get($this->key)) && ($value != $this->lock_id));
    }


    /**
     * Acquire lock.
     *
     * @return boolean
     */
    public function acquire()
    {
        $this->cache_object->set($this->key, $this->lock_id, self::TTL);
    }

    /**
     * Releases the lock.
     *
     * @return boolean
     */
    public function release()
    {
        unset(self::$instances[$this->key]);

        return $this->cache_object->delete($this->key);
    }

    /**
     * Release cache lock before object's destruction.
     *
     * If Exceptions, reDispatch, exit() or other hacks interfere with the normal workflow
     * the cache locks have to be released.
     */
    public function __destruct()
    {
        if (!empty(self::$instances[$this->key])) {
            $this->release();
        }
    }
}
