<?php
use Sifo\Http\Filter\FilterServer;

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
	$_current_querystring = FilterServer::getInstance()->getString( 'QUERY_STRING' );
	$_current_path = FilterServer::getInstance()->getString( 'REQUEST_URI' );

	if ( !empty( $_current_querystring ) )
	{
		$_current_querystring = '?' . $_current_querystring;
		$_current_path = str_replace( $_current_querystring, '', $_current_path );
	}

	if ( !isset( $params['page'] ) )
	{
		trigger_error( 'pagelink: You should provide the destination pagelink.' );
	}
	else
	{
		if ( $params['page'] > 1 )
		{
			return $_current_path . $_delimiter . $params['page'];
		}
		else
		{
			return $_current_path;
		}
	}
}
