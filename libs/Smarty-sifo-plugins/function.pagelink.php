<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_pagelink( $params, &$smarty )
{

	if ( isset( $params['delimiter'] ) )
	{
		$_delimiter = $params['delimiter'];
		unset( $params['delimiter'] );
	}
	else
	{
		$_delimiter = ':';
	}
	$_current_querystring = \Sifo\FilterServer::getInstance()->getString( 'QUERY_STRING' );
	$_current_path = \Sifo\FilterServer::getInstance()->getString( 'REQUEST_URI' );

	if ( !empty( $_current_querystring ) )
	{
		$_current_querystring = '?' . $_current_querystring;
		$_current_path = str_replace( $_current_querystring, '', $_current_path );
	}

	$_current_url = array_reverse( explode( $_delimiter, $_current_path ) );

	if ( is_numeric( $_current_url[0] ) )
	{
		$_current_page = ( int )array_shift( $_current_url );
	}
	else
	{
		$_current_page = 1;
	}

	$_current_url = implode( $_delimiter, array_reverse( $_current_url ) );

	if ( !isset( $params['page'] ) )
	{
		trigger_error( "pagelink: You should provide the destination pagelink." );
	}
	else
	{
		if ( $params['page'] > 1 )
		{
			return $_current_url . $_delimiter . $params['page'];
		}
		else
		{
			return $_current_url;
		}
	}
}

/* vim: set expandtab: */

?>
