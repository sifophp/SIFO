<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty default modifier plugin
 *
 * Type:     modifier<br>
 * Name:     default<br>
 * Purpose:  designate default value for empty variables
 * @link http://smarty.php.net/manual/en/language.modifier.default.php
 *          default (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_normalize($string, $strict = false)
{
	if ( class_exists( '\Sifo\Urls' ) && method_exists( '\Sifo\Urls', 'normalize' ) )
	{
		$_urlized_val = \Sifo\Urls::normalize( $string );
	}
	else
	{
		$_urlized_val = $string;		
	}
	
	return $_urlized_val;
}

/* vim: set expandtab: */

?>
