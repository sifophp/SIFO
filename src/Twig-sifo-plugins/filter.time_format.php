<?php

function twig_filter_time_format()
{
    return new \Twig_Filter(
        'time_format', function (
        $time,
        $decimals = 3
    ) {
        // Numeric locale vars.
        // Remember to change the number_format modifier if you change the locales management here.
        setlocale(LC_NUMERIC, \Sifo\Domains::getInstance()->getLanguage());
        $locale = localeconv();
        setlocale(LC_NUMERIC, null);
        $thousand_separator = ($locale['thousands_sep'] == '') ? '.' : $locale['thousands_sep'];
        $decimal_separator  = $locale['decimal_point'];

        if (null === $decimals)
        {
            $decimals = 0;
        }

        $time *= 1000;

        if ($time < 100)
        {
            // Miliseconds.
            $formatted_time = number_format($time, $decimals, $decimal_separator, $thousand_separator) . ' milisec';
        }
        else
        {
            // Seconds.
            $formatted_time = number_format($time / 1000, $decimals, $decimal_separator, $thousand_separator) . ' sec';
        }

        return $formatted_time;
    }
    );
}
