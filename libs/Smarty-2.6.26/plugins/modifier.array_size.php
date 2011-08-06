<?php
/**
 * Smarty plugin.
 *
 * @package Smarty
 * @subpackage Plugins
 * @author Basilio Vera
 */

/**
 * Smarty debug query time modifier.
 *
 * Type:     modifier<br>
 * Name:     debug_query_time<br>
 * Purpose:  Changes the style of the number if $time is higher than one defined limit.
 *
 * @param float $time Time expressed in seconds.
 * @param float $limit Limit to use the HIGHLIGHTED style or not.
 * @return string
 */
function smarty_modifier_array_size( $array )
{
	return strlen( serialize( $array ) );
}

?>