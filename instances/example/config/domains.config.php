<?php

$config['core_inheritance'] = array( 'Sifo' );

$config['redirections'] = array(
	array( 'from' => 'www.sifo.local', 'to' => 'http://sifo.local' ),
	array( 'from' => 'www2.sifo.local', 'to' => 'http://sifo.local' ),
);

$config['instance_type'] = 'instantiable';

$config['sifo.local'] = array(
	'devel' => true, // Domain is marked as development
	'has_debug' => true, // Domain shows the floating debug box.
	'instance' => 'example',
	'language' => 'en_US',
	'language_domain' =>'messages',
	'lang_in_subdomain' => array( 'es' => 'es_ES', 'en' => 'en_US' ),
	'www_as_subdomain' => false,
	'static_host' => 'http://static.sifo.local',
	'media_host' => 'http://static.sifo.local', // Alternative static content (media). Comment to disable.
	'database' => array(
		'db_driver' => 'mysql', // To use transactions you must use mysqli driver.
		'db_host' => '127.0.0.1',
		'db_user' => 'root',
		'db_password' => 'root',
		'db_name' => 'yourdatabase',
		'db_init_commands' => array( 'SET NAMES utf8' ) // Commands launched before the queries.
	),
	'php_ini_sets' => array( // Empty array if you don't want any php.ini overriden.
		'log_errors' => 'On',
		'error_log' => ROOT_PATH . '/logs/errors_' . date( 'Y-m' ) . '.log', // Store a different error file per month. For the lazy rotator :)
	),
);
