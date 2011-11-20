<?php
/**
 * Smarty plugin.
 *
 * @package Smarty
 * @subpackage Plugins
 * @author   Basilio Vera
 */

/**
 * Smarty time_format modifier plugin.
 *
 * Type:     modifier<br>
 * Name:     time_format<br>
 * Purpose:  format times directly from DB.
 *
 * @param integer $time Time given in ms.
 * @param integer $decimals Number of decimals.
 * @return string
 */
function smarty_modifier_time_format( $time, $decimals=3 )
{
	// Numeric locale vars.
	// Remember to change the number_format modifier if you change the locales management here.
	setlocale( LC_NUMERIC, \Sifo\Domains::getInstance()->getLanguage() );
	$locale = localeconv();
	setlocale( LC_NUMERIC, null );
	$thousand_separator = ( $locale['thousands_sep'] == '' ) ? '.' : $locale['thousands_sep'];
	$decimal_separator = $locale['decimal_point'];

	if ( is_null( $decimals ) )
	{
		$decimals = 0;
	}
	
	$time = $time*1000;

	if ( $time < 100 )
	{
		// Miliseconds.
		$formatted_time = number_format( $time, $decimals, $decimal_separator, $thousand_separator ).' milisec';
	}
	else
	{
		// Seconds.
		$formatted_time = number_format( ( $time / 1000 ), $decimals, $decimal_separator, $thousand_separator ).' sec';
	}

	return $formatted_time;
}

/* vim: set expandtab: */

?>
