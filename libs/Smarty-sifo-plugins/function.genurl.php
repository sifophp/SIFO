<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {genurl} function plugin
 *
 * Type:     function<br>
 * Name:     genurl<br>
 * Input:<br>
 *           - [any]				(required) - string
 *           - subject				(required) - string
 *           - action				(required) - string (add|replace|remove)
 *           - params				(required) - array
 *           - params_definition	(optional) - array
 *           - key                  (optional) - string
 *           - value                (optional, but if "value" is defined, "key" is mandatory!) - string
 *           - delimiter            (optional, defaults to '%' ) - string
 *			 - normalize	        (optional, set to normalize=no if you don't want normalize) - string
 * Purpose:  Add URL params to URL given in 'subject' using given delimiter to separate them.
 *           If we pass a params_definition key7value pair for internal keys, we replace them with right key.
 *
 * 			 Warning:
 * 				The arguments "key" and "value" are reserved words. Please don't use them as URL definition parameters!.
 *
 *           Examples of usage:
 *
 *           {genurl subject=$url.list action='replace' params=$params params_definition=$params_definition show='big'}
 *           Output: http://domain.com/list:o:big
 *
 *           {genurl subject=$url.list action='add' params=$params params_definition=$params_definition show='medium'}
 *           Output: http://domain.com/list:o:big,medium
 *
 *           {genurl subject="`$url.search`/`$keyword`" action='replace' key='country' value='sri-lanka' params=$params params_definition=$params_definition}
 *           Output: http://domain.com/search/keyword:c:sri-lanka
 *
 * @author Albert Garcia
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_genurl( $params, &$smarty )
{

	if ( isset( $params['delimiter'] ) )
	{
		$_delimiter = $params['delimiter'];
		unset( $params['delimiter'] );
	}
	else
	{
		$_delimiter = ':';
	}

	if ( false !== strpos( $_delimiter, '$' ) )
	{
		trigger_error( "fill: The delimiter '$' is banned in function {url}", E_USER_NOTICE );
	}

	$action = ( isset( $params['action'] ) ) ? $params['action'] : 'replace';

	// You can also specify {genurl key='filter_name' value='filter_value'} instead of {genurl filter_name='filter_value'}.
	// This is useful when you have dynamic filtering.
	if ( !empty( $params['key'] ) && isset( $params['value'] ) )
	{
		$params[$params['key']] = $params['value'];
	}

	$url_params = $params['params'];
	$url_params_definition = $params['params_definition'];

	// Build $order based in params_definition position for each key.
	if ( is_array( $url_params_definition ) )
	{
		$n = 0;
		foreach ( $url_params_definition as $key => $value )
		{
			$order[$key] = $n;
			$n++;
		}
	}
	
   	$_html_result = $params['subject'];
   	$_original_html_result = $_html_result;

	$normalize = ( !isset($params['normalize'] ) || $params['normalize'] != 'no' );
	unset( $params['action'] );
	unset( $params['normalize'] );
	unset( $params['params'] );
	unset( $params['params_definition'] );
	unset( $params['subject'] );
	unset( $params['key'] );
	unset( $params['value'] );

	// Step 1: Fill $url_params with actual and new values.

	if ( $action == 'replace' ) // Replace actual value with new one.
	{
		foreach ( $params as $key => $value )
		{
			$url_params[$key] = $value;
			if ( true === $url_params_definition[$key]['is_list'] )
			{
				$url_params[$key] = array( $value );
			}
		}
	}
	elseif ( $action == 'clean_params' )
	{
		$url_params = array( );
		foreach ( $params as $key => $value )
		{
			$url_params[$key] = $value;
			if ( true === $url_params_definition[$key]['is_list'] )
			{
				$url_params[$key] = array( $value );
			}
		}
	}
	elseif ( $action == 'add' ) // Add actual value with new one.
	{
		foreach ( $params as $key => $value )
		{
			$url_params[$key][] = $value;
		}
	}
	elseif ( $action == 'remove' )
	{
		foreach ( $params as $key => $value )
		{
			if ( true === $url_params_definition[$key]['is_list'] )
			{
				$found_key = array_search( strtolower( $value ), $url_params[$key] );
				if ( false !== $found_key )
				{
					unset( $url_params[$key][$found_key] );
				}
			}
			else
			{
				if ( is_array( $url_params ) && array_key_exists( $key, $url_params ) )
				{
					unset( $url_params[$key] );
				}
			}
		}
	}


	// Step 2: translate actual params to right key=>value pairs based on url definition.
	$n = 0;

	if ( is_array( $url_params ) )
	{
		foreach ( $url_params as $_key => $_val )
		{
			if ( is_array( $_val ) )
			{
				foreach ( $_val as $__key => $__val )
				{
					if ( true === $url_params_definition[$_key]['apply_translation'] )
					{
						$current_domain = \Sifo\I18N::getDomain();
						\Sifo\I18N::setDomain( 'urlparams', \Sifo\I18N::getLocale() );
						$_val[$__key] = \Sifo\I18N::getTranslation( $__val );
						\Sifo\I18N::setDomain( $current_domain, \Sifo\I18N::getLocale() );
					}
					else
					{
						$_val[$__key] = $__val;
					}
					if ( $normalize )
					{
						$_val[$__key] = \Sifo\Urls::normalize( $_val[$__key] );
					}

				}
				// Ordering values list:
				sort( $_val );
				$_val = implode( ',', $_val );
			}
			elseif ( true === $url_params_definition[$_key]['apply_translation'] )
			{
				$current_domain = \Sifo\I18N::getDomain();
				\Sifo\I18N::setDomain( 'urlparams', \Sifo\I18N::getLocale() );
				$_val = \Sifo\I18N::getTranslation( $_val );
				\Sifo\I18N::setDomain( $current_domain, \Sifo\I18N::getLocale() );
				if ( $normalize )
				{
					$_val = \Sifo\Urls::normalize( $_val );
				}

			}
			elseif ( $normalize )
			{
				$_val = \Sifo\Urls::normalize( $_val );
			}

			if ( isset( $_val ) && '' != $_val && false !== $_val )
			{
				$n++;

				if ( array_key_exists( $_key, $url_params_definition ) )
				{
					$_html_filters[$_key] = $url_params_definition[$_key]['internal_key'] . $_delimiter . $_val;
				}
				else
				{
					trigger_error( "fill: The parameter '" . $_key . "' is not defined in given params_definition", E_USER_NOTICE );
				}
			}
		}

		if ( isset( $_html_filters ) && is_array( $_html_filters ) )
		{
			// We alphabetically order the filters based on 'internal_key'
			// to prevent duplicated URL with the same parameters.
			ksort( $_html_filters );
			$_html_result .= $_delimiter . implode( $_delimiter, $_html_filters );
		}
	}

	if ( $n > 0 )
	{
		return $_html_result;
	}
	else
	{
		return $_original_html_result;
	}
}
