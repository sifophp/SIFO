<?php
/**
 * block.t.php - Smarty simulation of gettext block plugin
 *
 * Modified to use PHP Array intead of gettext. In Windows gettext is not supported.
 */

/**
 * Replaces arguments in a string with their values.
 * Arguments are represented by % followed by their number.
 *
 * @param	string	Source string
 * @param	mixed	Arguments, can be passed in an array or through single variables.
 * @returns	string	Modified string
 */
function smarty_gettext_strarg($str)
{
	$tr = array();
	$p = 0;

	for ($i=1; $i < func_num_args(); $i++) {
		$arg = func_get_arg($i);

		if (is_array($arg)) {
			foreach ($arg as $aarg) {
				$tr['%'.++$p] = $aarg;
			}
		} else {
			$tr['%'.++$p] = $arg;
		}
	}

	return strtr($str, $tr);
}

/**
 * Smarty block function, provides gettext support for smarty.
 *
 * The block content is the text that should be translated.
 *
 * Any parameter that is sent to the function will be represented as %n in the translation text,
 * where n is 1 for the first parameter. The following parameters are reserved:
 *   - escape - sets escape mode (only available when the smarty escape_html attribute is false):
 *
 *       - 'html' for HTML escaping, this is the default.
 *       - 'js' for javascript escaping.
 *       - 'url' for url escaping.
 *       - 'no'/'off'/0 - turns off escaping
 *   - escapevar (Set to no for avoid html escaping when the smarty escape_html attribute is true).
 *       Prevents escaping of the variables passed. DANGEROUS if variables might be originated using user input (XSS)
 *   - plural - The plural version of the text (2nd parameter of ngettext())
 *   - count - The item count for plural mode (3rd parameter of ngettext())
 */
function smarty_block_t($params, $text, &$smarty)
{
	$text = stripslashes($text);

	// set escape mode (Escape use is not available when your have to set to true the escape_html flag)
	if( ( $smarty == null ) || ( !$smarty->escape_html ) )
	{
		// In the don't protected configuration, the expectd behaviour is escapeing html:
		$escape = ( isset($params['escape'] ) )? $params['escape'] : "html";
		unset($params['escape']);

		if( isset( $params['escapevar'] ) )
		{
			// Option not available when escape_html is false. You could use $var|escape
			unset( $params['escapevar'] );
		}
	}
	else
	{
		$escape = false;
		if ( isset($params['escape'] ) )
		{
			// (escape) Escape param is not available when escape_html is set to true.
			unset($params['escape']);
		}

		// set escape_var mode
		if ( ( !isset( $params['escapevar'] ) ) || ( $params['escapevar']!='no') )
		{
			foreach( $params as &$param )
			{
				$param = htmlspecialchars($param, ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET );
			}
		}
		elseif( isset( $params['escapevar'] ) )
		{
			unset( $params['escapevar'] );
		}
	}

	// set plural version
	if (isset($params['plural'])) {
		$plural = $params['plural'];
		unset($params['plural']);

		// set count
		if (isset($params['count']) && ( 1 != $params['count'] ) ) {
			$text = $plural;
			unset($params['count']);
		}
	}

	$text = \Sifo\I18N::getTranslation( $text );

	// run strarg if there are parameters
	if (count($params)) {
		$text = smarty_gettext_strarg($text, $params);
	}

	switch ($escape) {
		case 'html':
			$text = nl2br(htmlspecialchars($text));
			break;
		case 'javascript':
		case 'js':
			// javascript escape
			$text = str_replace('\'', '\\\'', stripslashes($text));
			break;
		case 'url':
			// url escape
			$text = urlencode($text);
			break;
	}

	return $text;
}

?>
