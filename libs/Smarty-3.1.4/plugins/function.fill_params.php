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
 * Name:     fill_params<br>
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
 * @author Albert Garcia
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_fill_params($params, &$smarty)
{

    if ( isset($params['delimiter']) )
    {
        $_delimiter = $params['delimiter'];
        unset($params['delimiter']);
    } else {
        $_delimiter = ':';
    }

    if ( false !== strpos($_delimiter, '$' ) )
    {
         $smarty->trigger_error("fill: The delimiter '$' is banned in function {url}", E_USER_NOTICE);
    }
    
	$action = ( isset( $params['action'] ) ) ? $params['action'] : 'replace';

	$filters = $params['params'];

   	$_html_result 			= $params['subject'];
   	$_original_html_result 	= $_html_result;
	
	unset( $params['action'] );
	unset( $params['params'] );
	unset( $params['subject'] );
	
	if ( $action == 'replace' )
	{
		foreach ( $params as $key=>$value )
		{
			switch( $key )
			{
				case 'countries':
				case 'regions':
				case 'grapes':
				case 'appellations':
				{
					$filters[$key] = array( $value );
				}break;
				default:
				{
					$filters[$key] = $value;
				}break;
			}
		}
	}
	elseif ( $action == 'add' )
	{
		foreach ( $params as $key=>$value )
		{
			$filters[$key][] = $value;
		}
	}
	elseif ( $action == 'remove' )
	{
		foreach ( $params as $key=>$value )
		{
			switch( $key )
			{
				case 'countries':
				case 'regions':
				case 'grapes':
				case 'appellations':
				{
					$found_key = array_search( strtolower($value), $filters[$key] );
					if ( false !== $found_key )
						unset( $filters[$key][$found_key] );
				}break;
				default:
				{
					if ( is_array( $filters ) && array_key_exists( $key, $filters ) )
						unset( $filters[$key] );
				}break;
			}
		}
	}
			
    $n = 0;

	if ( is_array ( $filters ) )
	{
		foreach($filters as $_key => $_val)
		{
			if ( is_array( $_val ) )
			{
				$_val = implode(',',$_val);
			}
			elseif ( $_key == 'order' || $_key == 'onsale' )
			{
				$_val = \Sifo\I18N::getTranslation( $_val );
			}
			
			if ( method_exists( UtilsUvinum, 'normalize' ) )			
			{
				$_val = UtilsUvinum::normalize( $_val );
			}
			
			if ( !empty( $_val ) )
			{
				$n++;
				switch ( $_key )
				{
					case 'type': 
					{
						$_prefix = 't';
						$order[$_key] = 2;
					}break;
					case 'price': 
					{
						$_prefix = 'p'; 
						$order[$_key] = 7;
					}break;
					case 'rank': 
					{
						$_prefix = 's'; 
						$order[$_key] = 8;
					}break;
					case 'countries':
					{
						$_prefix = 'c'; 
						$order[$_key] = 5;
					}break;
					case 'regions':
					{
						$_prefix = 'r';
						$order[$_key] = 4;
					}break;
					case 'grapes':
					{
						$_prefix = 'g';
						$order[$_key] = 6;
					}break;
					case 'appellations':
					{
						$_prefix = 'a';
						$order[$_key] = 3;
					}break;
					case 'order':
					{
						$_prefix = 'o';
						$order[$_key] = 9;
					}break;
					case 'onsale':
					{
						$_prefix = 'v';
						$order[$_key] = 1;
					}break;
					case 'food':
					{
						$_prefix = 'f';
						$order[$_key] = 10;
					}break;
					default: 
					{
						$_prefix = $_key;
						$order[$_key] = 100;
					}break;
				}
				
				$_html_filters[$_key] = $_prefix . $_delimiter . $_val;
			}
	    }
	    
	    // We order the filters based on $order array;
	    if ( is_array( $_html_filters ) )
	    {
		    array_multisort( $order, SORT_ASC, $_html_filters );
		    $_html_result .= $_delimiter . implode( $_delimiter, $_html_filters );		    
	    }
	}
    
    if ( $n > 0 )
		return $_html_result;
	else
		return $_original_html_result;
}

/* vim: set expandtab: */

?>
