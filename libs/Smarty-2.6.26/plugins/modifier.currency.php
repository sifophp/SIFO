<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty currency modifier plugin
 *
 * Type:     modifier<br>
 * Name:     currency<br>
 * Purpose:  Formats a number as a currency string, with the given currency symbol
 * @param float
 * @param string currency (default EUR)
 * @return string
 */
function smarty_modifier_currency($amount, $currency='EUR', $tag='')
{
	$currency_symbols = array(
		'EUR' => '€',
		'GBP' => '£',
		'USD' => '$'
	);
	
	if ( array_key_exists( $currency, $currency_symbols ) )
	{
		$currency_symbol = $currency_symbols[$currency];
	}
	else
	{
		$currency_symbol = $currency;
	}
	
	if ( !empty( $tag ) )
	{
		$currency_symbol = '<'.$tag.'>'.$currency_symbol.'</'.$tag.'>';
	}
	
	switch( $currency )
	{
		case 'USD':
		case 'GBP':
		{
			return $currency_symbol.number_format($amount,2,'.',',');
		}
		
		default:
		{
			return number_format($amount,2,',','.').$currency_symbol;
		}
	}
}

?>
