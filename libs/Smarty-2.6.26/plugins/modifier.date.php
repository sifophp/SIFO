<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty date modifier plugin
 *
 * Type:     modifier
 * Name:     date
 * Purpose:  format datestamps via PHP's date()
 * Input:
 *         - timestamp: date in timestamp format
 *         - format: date() format for output
 * @author   Manuel Fernandez (with big effort)
 * @param string
 * @return string|void
 */
function smarty_modifier_date($timestamp, $format = '%d/%m/%Y')
{
	return date( $format, (int)$timestamp );
}

/* vim: set expandtab: */

?>
