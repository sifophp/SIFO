<?php

// Sets if memcache is active or not. If disabled, the system will cache to disk (if the application uses caching).
$config['active'] = false;

// Whether to use the Memcache client, or the new Memcached client.
$config['client'] = 'Memcache'; // Memcache or Memcached.

// List of available memcache servers, format: 'host' => 'port'
$config['servers'][] = array( '127.0.0.1' => '11211' );