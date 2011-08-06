<?php
/**
 * Point of entry for this instance. Apache points to this file.
 *
 * All PHP requests should be directed to this file via Apache using mod_rewrite or mod_alias or .htaccess (not really efficient).
 */

// Get the path to the root folder. ( 4 folders up from the current file):
$root = realpath( dirname( __FILE__ ) . '/../../../..' );

// Instance name (folder under instances):
$instance = 'default';

// Define the root path:
define( 'ROOT_PATH', $root );

require ROOT_PATH . '/instances/Bootstrap.php';

// Execute your instance:
Bootstrap::execute( $instance );
