<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

include_once( 'modifier.escape.php' );

/**
 * Smarty alias for 'escape:htmlall'
 *
 * @param string
 * @return string
 */
function smarty_modifier_e($string)
{
	return smarty_modifier_escape( $string, 'htmlall', 'utf-8' );
}

/* vim: set expandtab: */

?>
