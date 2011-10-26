<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {pagelink} function plugin
 *
 * @param array
 * @param Smarty
 * @return string
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

	if ( class_exists( 'FilterServer' ) && method_exists( 'FilterServer', 'getString' ) )
	{
		$_actual_querystring = FilterServer::getInstance()->getString( 'QUERY_STRING' );
		$_actual_path = FilterServer::getInstance()->getString( 'REQUEST_URI' );
		$_actual_host = UrlParser::$base_url;
	}
	else
	{
		$_actual_querystring = $_SERVER['QUERY_STRING'];
		$_actual_path = $_SERVER['REQUEST_URI'];
		$_actual_host = ( ( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];
	}

	if ( !empty( $_actual_querystring ) )
	{
		$_actual_querystring = '?' . $_actual_querystring;
		$_actual_path = str_replace( $_actual_querystring, '', $_actual_path );
	}

	$_actual_url = array_reverse( explode( $_delimiter, $_actual_path ) );

	if ( is_numeric( $_actual_url[0] ) )
	{
		$_actual_page = ( int ) array_shift( $_actual_url );
	}
	else
	{
		$_actual_page = 1;
	}

	$_actual_url = implode( $_delimiter, array_reverse( $_actual_url ) );

	if ( isset( $params['absolute'] ) )
	{
		$_actual_url = $_actual_host . $_actual_url;
	}

	if ( !isset( $params['page'] ) )
	{
		$smarty->trigger_error( "pagelink: You should provide the destination pagelink." );
	}
	else
	{
		if ( $params['page'] > 1 || ( isset( $params['force_first_page'] ) && $params['force_first_page'] == true ) )
		{
			return $_actual_url . $_delimiter . $params['page'];
		}
		else
		{
			return $_actual_url;
		}
	}
}