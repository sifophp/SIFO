<?php

function twig_filter_number_format()
{
    return new \Twig_Filter(
        'number_format', function (
        $string,
        $decimals = 2
    ) {
        setlocale(LC_NUMERIC, \Sifo\Domains::getInstance()->getLanguage());
        $locale = localeconv();
        setlocale(LC_NUMERIC, null);
        $thousand_separator = ($locale['thousands_sep'] == '') ? '.' : $locale['thousands_sep'];
        $decimal_separator  = $locale['decimal_point'];

        return @utf8_encode(number_format($string, $decimals, $decimal_separator, $thousand_separator));
    }
    );
}
