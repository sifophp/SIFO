<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty create_links modifier plugin
 *
 * Type:     modifier<br>
 * Name:     create_links<br>
 * Purpose:  create HTML markup for links<br>
 * Input:<br>
 *         - string: text you want to search for links into
 * Params:<br>
 *         - string $regexp Optional in case you want to define a different regexp for URLs (like only internal links).
 *
 * @author   Manuel Fernandez <un tio muy chungo>
 * @return string
 */
function smarty_modifier_create_links($string,$regexp = '')
{
	if ( empty( $regexp ) )
	{
		$regexp = <<<REGEXP
{
  \\b
  # Match the leading part (proto://hostname, or just hostname)
  (
    # http://, or https:// leading part
    (https?)://[-\\w]+(\\.\\w[-\\w]*)+
  |
    # or, try to find a hostname with more specific sub-expression
    (?i: [a-z0-9] (?:[-a-z0-9]*[a-z0-9])? \\.)+ # sub domains
    # Now ending .com, etc. For these, require lowercase
    (?-i: com\\b
        | edu\\b
        | biz\\b
        | gov\\b
        | in(?:t|fo)\\b # .int or .info
        | mil\\b
        | net\\b
        | org\\b
        | [a-z][a-z]\\.[a-z][a-z]\\b # two-letter country code
    )
  )

  # The rest of the URL is optional, and begins with /
  (
    /
    # The rest are heuristics for what seems to work well
    [^.!,?;"\\'<>()\[\]\{\}\s\x7F-\\xFF]*
    (
      [.!,?]+ [^.!,?"\\'<>()\\[\\]\{\\}\s\\x7F-\\xFF]+
    )*
  )?
}ix
REGEXP;
	}

	return preg_replace( $regexp, '<a href="\1\4">\1\4</a>', $string );
}

/* vim: set expandtab: */

?>
