<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate_words modifier plugin
 *
 * Type:     modifier
 * Name:     truncate_words
 * Purpose:  Truncate a string by number of characters and later number of words
 * @author   Carlos Ballesteros <carlos.ballesteros@softonic.com>
 * @param string
 * @param integer
 * @param integer
 * @return string
 */
function smarty_modifier_truncate_words( $string, $words_max = 0x7FFFFFFF, $char_maxlength = 0x7FFFFFFF )
{
	$params = preg_split( '/([\\.,:;!?\\s]+)/', substr( $string, 0, $char_maxlength ), -1, PREG_SPLIT_DELIM_CAPTURE );
	return implode( '', array_slice( $params, 0, ( ( $words_max * 2 ) - 1 ) ) );
}

/* vim: set expandtab: */

?>
