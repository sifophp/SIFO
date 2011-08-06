<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     search_links<br>
 * Date:     28 Dic, 2010
 * Purpose:  In a plain text convert links to HTML.
 * Example:  {$text|search_links}
 * @link http://www.harecoded.com
 * @version  1.0
 * @author   Albert Lombarte <alombarte@harecoded.com>
 * @param string
 * @return string
 */
function smarty_modifier_search_links( $text, $title = '', $rel='nofollow' )
{
	preg_match_all( '/(http|ftp)+(s)?:(\/\/)((\w|\.)+)(\/)?(\S+)?(\.*)?/i', $text, $matches );
	if ( !empty( $matches[0] ) )
	{
		array_unique( $matches[0] );
		foreach ( $matches[0] as $url )
		{
			$text = str_replace( $url, "<a title=\"$title\" href=\"$url\" rel=\"$rel\" class=\"url\">$url</a>", $text );
		}
	}

	return $text;

}

?>