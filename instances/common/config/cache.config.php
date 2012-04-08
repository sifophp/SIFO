<?php

// Sets if memcache is active or not. If disabled, the system will cache to disk (if your application uses any caching).
$config['active'] = false;

// Whether to use the Memcache client, or the new Memcached client (see your PHP.ini)
$config['client'] = 'Memcache'; // Memcache or Memcached.

// List of available memcache servers, format: 'host' => 'port'
$config['servers'][] = array( '127.0.0.1' => '11211' );

/**
 * Tags definition:
 *
 * Tags are a label used in your cache usage that allows you to group
 * cache keys. By doing this when you delete a tag all the associated
 * cache keys with this label (or tag) are aso deleted.
 */
$config['cache_tags'] = array( // Sample tags
	'id_user',
	'type'
);