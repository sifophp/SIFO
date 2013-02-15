<?php
/**
 * View engine config settings.
 *
 *
 * $config['VIEW_ENGINE']['PARAM_NAME'] = VALUE
 *
 */

$config['smarty'] = array(
	'custom_plugins_dir' => null, // Used to custom the default smarty plugins directory.

	// Set this to false to avoid magical parsing of literal blocks without the {literal} tags.
	'auto_literal' => false,

	// Set this to false to avoid auto escape html strings.
	'escape_html' => false,
);
