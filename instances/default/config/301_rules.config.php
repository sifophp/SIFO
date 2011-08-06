<?php
/**
 * Predefined 301 jumps.
 *
 * This config is required before launch the NO_ROUTE_FOUND behaviour in Router class.
 * Used to define 301 jumps from old to new urls.
 *
 * Description:
 * $config[ string $regexp] = string $url_definition
 *
 * Params:
 *	regexp
 *		Regular expression to identify the affected url's.
 *
 *	url_definition
 *		Destiny url using the param's defined in regexp.
 *
 * Example:
 *
 * Given the (old) address: http://www.whatdoyouwant.com?search=doggie_and_kittens
 * And the (new) address: http://doggie_and_kittens.whatdoyouwant.com 
 * We could define this rule:
 *		$config['/www.whatdoyouwant.com\/?\?search=(.+)$/'] = '$1.whatdoyouwant.com';
 *
 *
 * Trick:
 * You can use the Jordi Rivero's useful site to test your definitions. @link: http://xrg.es
 */
// ____________________________________________________________________________

$config = array();
