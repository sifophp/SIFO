<?php
/**
 * Smarty plugin.
 *
 * @package Smarty
 * @subpackage Plugins
 * @author   Albert
 */

include_once('block.t.php');

/**
 * Smarty size_format modifier plugin.
 *
 * Type:     modifier<br>
 * Name:     size_format<br>
 * Purpose:  format sizes directly from DB.
 *
 * @param integer $size Size of a file.
 * @param integer $decimals Number of decimals.
 * @return string
 */
function smarty_modifier_time_since( $diff_time )
{
	if ( !is_numeric ( $diff_time ) )
		$diff_time = strtotime( date("Y-m-d H:i:s") ) - strtotime($diff_time);

	if ( floor($diff_time/(60*60*24)) >= 365 )
	{
        $value[0] = floor($diff_time/(60*60*24*365));
        if ( 1 == $value[0] )
	        $value[1] = \Sifo\I18n::getTranslation("year");
	    else
	        $value[1] = \Sifo\I18n::getTranslation("years");
	}
	elseif ( floor($diff_time/(60*60*24)) >= 30 )
	{
        $value[0] = floor($diff_time/(60*60*24*30));
        if ( 1 == $value[0] )
	        $value[1] = \Sifo\I18n::getTranslation("month");
	    else
	        $value[1] = \Sifo\I18n::getTranslation("months");
	}
	elseif ( floor($diff_time/(60*60*24)) >= 7 )
	{
        $value[0] = floor($diff_time/(60*60*24*7));
        if ( 1 == $value[0] )
	        $value[1] = \Sifo\I18n::getTranslation("week");
	    else
	        $value[1] = \Sifo\I18n::getTranslation("weeks");
	}
	elseif ( floor($diff_time/(60*60)) >= 24 )
	{
        $value[0] = floor($diff_time/(60*60*24));
        if ( 1 == $value[0] )
	        $value[1] = \Sifo\I18n::getTranslation("day");
	    else
	        $value[1] = \Sifo\I18n::getTranslation("days");
	}elseif ( floor($diff_time/(60)) >= 60 )
    {
        $value[0] = floor($diff_time/(60*60));
        if ( 1 == $value[0] )
	        $value[1] = \Sifo\I18n::getTranslation("hour");
	    else
	        $value[1] = \Sifo\I18n::getTranslation("hours");
    }
    elseif ( floor($diff_time/(60)) >= 1 )
    {
        $value[0] = floor($diff_time/(60));
        if ( 1 == $value[0] )
	        $value[1] = \Sifo\I18n::getTranslation("minute");
	    else
	        $value[1] = \Sifo\I18n::getTranslation("minutes");
    }
    else
    {
        $value[0] = false;
        $value[1] = \Sifo\I18n::getTranslation("just some seconds");
    }

	$value[1] = smarty_block_t( array( 'count' => $value[0] ), $value[1], $this, null );

    if ( $value[0] > 0 )
    {
        $params[1] = $value[0] . ' '. $value[1];
    }
    else
    {
        $params[1] = $value[1];
    }

	return smarty_block_t( $params, \Sifo\I18n::getTranslation( '%1 ago' ), $this, null );
;
}

/* vim: set expandtab: */

?>
