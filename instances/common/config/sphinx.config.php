<?php

// Use $config['profile_name'] to define a customized profile.

$config['default'] = array(
	// Sets if sphinx is active or not.
	'active' => false,

	// Connection to Sphinx server.
	'server' => '127.0.0.1',
	'port'   => 9312,
);

/**
 * It is possible to add more than one server to balance the Sphinx load in one profile.
 * In that case you have to define more than one server using this schema: one server for each array position.
 */
$config['default_balanced'] = array(
	// First sever.
	array(
		// Sets if sphinx is active or not.
		'active' => true,

		// Connection to Sphinx server.
		'server' => 'sphinx_1',
		'port'   => 9312,
		'weight' => 25
	),
	// Second sever.
	array(
		// Sets if sphinx is active or not.
		'active' => true,

		// Connection to Sphinx server.
		'server' => 'sphinx_2',
		'port'   => 9312,
		'weight' => 75
	),
	// Third sever...
	array(
		// Sets if sphinx is active or not.
		'active' => true,

		// Connection to Sphinx server.
		'server' => 'sphinx_3',
		'port'   => 9456,
		'weight' => 80
	),
);