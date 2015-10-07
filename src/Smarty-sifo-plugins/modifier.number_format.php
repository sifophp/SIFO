<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty number_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     number_format<br>
 * Purpose:  format strings via number_format
 * @author   Albert Lombarte
 * @param string
 * @param string
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_number_format( $string, $decimals = 2 )
{
	// Numeric locale vars.
	// Remember to change the size_format modifier if you change the locales management here.
	setlocale( LC_NUMERIC, \Sifo\Domains::getInstance()->getLanguage() );
	$locale = localeconv();
	setlocale( LC_NUMERIC, null );
	$thousand_separator = ( $locale['thousands_sep'] == '' ) ? '.' : $locale['thousands_sep'];
	$decimal_separator = $locale['decimal_point'];

	return @utf8_encode(number_format( $string, $decimals, $decimal_separator, $thousand_separator ));
}

/* vim: set expandtab: */

?>
