<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
use Sifo\Http\Urls;


/**
 * Smarty default modifier plugin
 *
 * Type:     modifier<br>
 * Name:     default<br>
 * Purpose:  designate default value for empty variables
 * @link http://smarty.php.net/manual/en/language.modifier.default.php
 *          default (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 *
 * Slightly modified to normalize using SIFO class.
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_normalize($string)
{
	$normalized_url = Urls::normalize( $string );

	return $normalized_url;
}
