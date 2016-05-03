<?php
namespace Sifo;

/**
 * Common methods available to every Cache instance.
 */
interface CacheContract
{
    /**
     * Check if Memcache is active and right connected
     *
     * @return integer
     */
    public function isActive();

    /**
     * Returns the content of the cache "key".
     *
     * @param $key
     *
     * @return mixed Cache content or false.
     */
    public function get($key);

    /**
     * Stores "$content" under "$key" for "$expiration" seconds.
     *
     * @param $key        string
     * @param $content    mixed
     * @param $expiration integer
     *
     * @return boolean
     */
    public function set($key, $content, $expiration);

    /**
     * Delete $key object from cache.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function delete($key);

    /**
     * Returns the cache string identifier after calculating all the tags and prepending the necessary attributes.
     *
     * @param array $definition Cache definition.
     *
     * @return string
     */
    public function getCacheKeyName(array $definition);

    /**
     * Delete cache from all the keys that contain the given tag in that value.
     *
     * @param string $tag   Cache tag.
     * @param mixed  $value Cache value.
     *
     * @return boolean Always returns true
     */
    public function deleteCacheByTag($tag, $value);
}
