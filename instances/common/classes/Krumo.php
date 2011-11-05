<?php

namespace Common;
/**
 * Alias of krumo::dump(). Formatted data dump. No output in production.
 * You need to download Krumo into "libs" first and declare it in "libraries.config.php"
 *
 * @param mixed $data,...
 */
function d( $var )
{
	// Enable Krumo only when debug is present.
	if ( \Sifo\Domains::getInstance()->getDevMode() )
	{
		require_once( ROOT_PATH . '/libs/'.\Sifo\Config::getInstance()->getLibrary( 'krumo' ) .'/class.krumo.php' );
		krumo( $var );
	}
	else
	{
		return false;
	}
}

/**
 * Trace a content to be dump in the debug screen.
 *
 * @param mixed $message The messsage.
 */
function trace( $message )
{
	$registry = \Sifo\Registry::getInstance();
	if ( $registry->keyExists( 'trace_messages' ) )
	{
		$trace_messages = $registry->get( 'trace_messages' );
	}
	$trace_messages[] = $message;
	$registry->set( "trace_messages", $trace_messages );
}

// Set a classname to allow Bootsrap::getClass()
class Krumo {}
?>
