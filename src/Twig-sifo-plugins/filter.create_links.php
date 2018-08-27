<?php

function twig_filter_create_links()
{
    return new \Twig_Filter(
        'create_links', function (
        $string,
        $regexp = ''
    ) {
        if (empty($regexp))
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

        return preg_replace($regexp, '<a href="\1\4">\1\4</a>', $string);
    }
    );
}
