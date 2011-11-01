<?php
/**
	Syntax of the domains:

	$config['domain.com'] = array(
	'devel' => true,
	'instance' => 'common',
	'auth' => 'user,password',	// User and password requested by the browser, comment to disable
	'trusted_ips' = 'ip1,ip2,...,ipn'; // When the auth directive is in use. Define here trusted user ip's for access without password.
	'lang_in_subdomain' => true, // The language is set in the subdomain. E.g: fr.subdomain.com
	'www_as_subdomain' => true, // Are you using "www" as a "default" subdomain or not.
	'language' => 'es', // Language by default, e.g. domain.es is always in Spanish
	'language_domain' =>'messages' // Name of the file containing the translations,
	'static_host' => 'http://static.sifo.local', // host containing the images and other static content
	'media_host' => 'http://media.sifo.local', // Avatars and other media not fitting under static. Comment or remove to disable.
	'database' => array(
		'db_driver' => 'mysql', // mysqli also available
		'db_host' => '127.0.0.1',
		'db_user' => 'root',
		'db_password' => 'root',
		'db_name' => 'mydatabase',
		'db_init_commands' => array( 'SET NAMES utf8' ),
		// 'profile' => 'PRODUCTION' // This option overrides all the previous keys and uses the profile set in db_profiles.config.php
		),
	// REDIS syntax:
	// 'redis' => array(
	//	'database' => array(
	//	'host'     => '127.0.0.1',
	//	'port'     => 6379,
	//	'database' => 0
	),

	'php_ini_sets' => array(  // Changes in php.ini conf. You'd better make changes in your php.ini and leave this array empty.
		'log_errors' => 'On',
		'error_log' => ROOT_PATH . '/logs/errors.log',
		'short_open_tag' => '1'
	).
	'libraries_profile' => 'default', // This profile defines the versions of libraries your project will use.
										// By default (if you don't add this variable in your domains.config) will use "common" profile.
	);
	Redirections use the EXACT term in the host, and needs the format
	$config['redirections'] = array( array( 'from' => 'domain.old', 'to' => 'http://domain.new' ), array( 'from' => 'domain2.old', 'to' => 'http://domain.new' ),... );
	FROM: is only the host while TO contains the protocol.

	Use $config['core_inheritance'] for active new versions of core.
		$config['core_inheritance'] = array( 'Sifo', 'Sifo5.3' );  // For work with SIFO for php5.3
 */;

$config['core_inheritance'] = array( 'Sifo' );
// Define the inheritance of this instance (which instances are their parents:
// $config['instance_inheritance'] = array( 'common', 'mygrandparent', 'myparent' );

$config['redirections'] = array(
	array( 'from' => 'www.sifo.local', 'to' => 'http://sifo.local' ),
	array( 'from' => 'www2.sifo.local', 'to' => 'http://sifo.local' ),
);

$config['sifo.local'] = array(
	'devel' => true,
	'instance' => 'common',
	'language' => 'en_US',
	'language_domain' =>'messages',
	'lang_in_subdomain' => array( 'es' => 'es_ES', 'en' => 'en_US' ),
	'www_as_subdomain' => false,
	'static_host' => 'http://static.sifo.local',
	'media_host' => 'http://static.sifo.local', // Alternative static content (media). Comment to disable.
	'database' => array(
		// If you need a master/slave schema enable the 'profile' line below:
		// 'profile' => 'PRODUCTION', // Use this option for MASTER/SLAVE configurations and fill db_profiles.config.php with credentials.
		'db_driver' => 'mysql', // To use transactions you must use mysqli driver.
		'db_host' => '127.0.0.1',
		'db_user' => 'root',
		'db_password' => 'root',
		'db_name' => 'yourdatabase',
		'db_init_commands' => array( 'SET NAMES utf8' ) // Commands launched before the queries.
	),
	/* REDIS syntax:
	'database' => array(
		'database' => array(
		'host'     => '127.0.0.1',
		'port'     => 6379,
		'database' => 0
	),
	 */
	'php_ini_sets' => array( // Empty array if you don't want any php.ini overriden.
		// Log errors to 'logs' folder:
		'log_errors' => 'On',
		'error_log' => ROOT_PATH . '/logs/errors_' . date( 'Y-m' ) . '.log', // Store a different error file per month. For the lazy rotator :)

		// Allow short tags <? (instead of <?php) for more flexible view scripts.
		// 'short_open_tag' => '1'
	),
	//'libraries_profile' => 'bleeding_edge' // What profile of libraries should be used.
);

$config['unit.test'] = $config['sifo.local'];
$config['unit.test']['instance'] = 'common';
