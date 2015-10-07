<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty link_urls plugin
 *
 * Type:     modifier<br>
 * Name:     link_urls<br>
 * Purpose:  performs a regex and replaces any url's with links containing themselves as the text
 * This could be improved by using a better regex.
 * And maybe it would be better for usability if the http:// was cut off the front?
 * @author Andrew
 * @return string
 */

function smarty_modifier_link_urls($string)
{
    $linkedString = preg_replace_callback("/\b(https?):\/\/([-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]*)\b/i",
                                create_function(
                                '$matches',
                                'return "<a href=\'".($matches[0])."\'>".($matches[0])."</a>";'
                                ),$string);

    return $linkedString;
}

?>