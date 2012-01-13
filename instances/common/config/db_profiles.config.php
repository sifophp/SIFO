<?php
/**
 * Database profiles used in a Master/Slave schema.
 *
 * You need to return a $config variable with the following FIXED Syntax:
 *
 * $config['PROFILE_NAME_INCLUDED_IN_DOMAINS']['master'] = array( $db_properties )
 * $config['PROFILE_NAME_INCLUDED_IN_DOMAINS']['slaves'] = array( array( $db_1_properties), array( $db_2_properties), ... )
 *
 */

$profile_production['master'] = array(
		'db_driver' => 'mysqli',
		'db_library' => 'adodb',
		'db_host' => 'dbmaster',
		'db_user' => 'myuser',
		'db_password' => 'xxxx',
		'db_name' => 'mydbname',
		'db_init_commands' => array( 'SET NAMES utf8' )
		);

$profile_production['slaves'] = array(
	array(
		'db_driver' => 'mysqli',
		'db_library' => 'adodb',
		'db_host' => 'dbslave1',
		'db_user' => 'myuser',
		'db_password' => 'xxxx',
		'db_name' => 'mydbname',
		'db_init_commands' => array( 'SET NAMES utf8' ),
		'weight' => 1000,
		),
	array(
		'db_driver' => 'mysqli',
		'db_library' => 'adodb',
		'db_host' => 'dbslave2',
		'db_user' => 'myuser',
		'db_password' => 'xxxx',
		'db_name' => 'mydbname',
		'db_init_commands' => array( 'SET NAMES utf8' ),
		'weight' => 500,
		)
	);

$config['PRODUCTION'] 	= $profile_production;
$config['DEVELOPMENT'] 	= $profile_production;