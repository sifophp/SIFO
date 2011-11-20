<?php
/**
 * The 'default' profile must always exist and contain any existing library.
 *
 * The rest of profiles EXTEND the default profile, so you only have to redeclare
 * the libraries that are different.
 */

/**
 * This classes will be loaded in this order and ALWAYS before starting.
 */
$config['classes_always_preloaded'] = array(
	'Exceptions',
	'Registry',
	'Filter',
	'Domains',
	'Urls',
	'Router',
	'Database',
	'Controller',
	'Model',
	'View',
	'I18N',
	'Benchmark',
	'Cache',
);

// Contains all the libraries available.
$config['default'] = array(
	'smarty' => 'Smarty-3.1.4',
	'adodb' => 'adodb5',
	'googleTranslate' => 'googleTranslate-1.7',
	'phpthumb' => 'PhpThumb',
	'phpmailer' => 'phpMailer_v5.1',
	'predis' => 'Predis',
	'sphinx' => 'Sphinx'
	//'krumo' => 'Krumo'
);

// Sample profile that redefines some libraries:
$config['bleeding_edge'] = array(
	'smarty' => 'Smarty-3.0rc2'
);