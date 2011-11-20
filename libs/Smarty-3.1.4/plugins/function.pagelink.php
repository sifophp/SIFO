<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {fill} function plugin
 *
 * Type:     function<br>
 * Name:     fill<br>
 * Input:<br>
 *           - [any]      (required) - string
 *           - subject       (required) - string
 *           - delimiter  (optional, defaults to '%' ) - string
 * Purpose:  Fills the variables found in 'subject' with the paramaters passed. The variables are any word surrounded by two delimiters.
 *           
 *           Examples of usage:
 *           
 *           {fill subject="http://domain.com/profile/%username%" username='fred'}
 *           Output: http://domain.com/profile/fred
 *
 *           {fill subject="Hello %user%, welcome aboard!" user=Fred}
 *           Outputs: Hello Fred, welcome aboard
 *
 *           {fill subject="http://||subdomain||.domain.com/||page||/||action||" subdomain='www' page='my-first-post' action='vote' delimiter='||'}
 *           Outputs: http://www.domain.com/my-first-post/vote
 *
 * @link    http://www.harecoded.com/fill-smarty-php-plugin-311577
 * @author Albert Lombarte <alombarte at harecoded dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_pagelink($params, &$smarty)
{

    if ( isset($params['delimiter']) )
    {
        $_delimiter = $params['delimiter'];
        unset($params['delimiter']);
    } else {
        $_delimiter = ':';
    }
	if ( class_exists( 'FilterServer' ) && method_exists( 'FilterServer', 'getString' ) )
	{
		$_actual_querystring = FilterServer::getInstance()->getString( 'QUERY_STRING' );
		$_actual_path = FilterServer::getInstance()->getString( 'REQUEST_URI' );
	}
	else
	{
		$_actual_querystring = $_SERVER['QUERY_STRING'];		
		$_actual_path = $_SERVER['REQUEST_URI'];	
	}
    
    if ( !empty( $_actual_querystring ) )
    {
    	$_actual_querystring = '?' . $_actual_querystring;    
    	$_actual_path = str_replace( $_actual_querystring,'',$_actual_path );
	}

	$_actual_url = array_reverse( explode( $_delimiter, $_actual_path ) );
	
	if ( is_numeric( $_actual_url[0] ) )
	{
		$_actual_page = (int)array_shift( $_actual_url );
	}
	else
	{
		$_actual_page = 1;
	}
	
	$_actual_url = implode( $_delimiter, array_reverse( $_actual_url ) );
	
	if ( !isset( $params['page'] ) )
	{
        $smarty->trigger_error("pagelink: You should provide the destination pagelink.");  
	}
	else
	{
		if ( $params['page'] > 1 )
		{
			return $_actual_url . $_delimiter . $params['page'];
		}
		else
		{
			return $_actual_url;
		}
	}
}

/* vim: set expandtab: */

?>
