<?php

function twig_filter_size_format()
{
    return new \Twig_Filter(
        'size_format', function (
        $size,
        $decimals = null
    ) {
        // Numeric locale vars.
        // Remember to change the number_format modifier if you change the locales management here.
        setlocale(LC_NUMERIC, \Sifo\Domains::getInstance()->getLanguage());
        $locale = localeconv();
        setlocale(LC_NUMERIC, null);
        $thousand_separator = ($locale['thousands_sep'] == '') ? '.' : $locale['thousands_sep'];
        $decimal_separator  = $locale['decimal_point'];

        if ($size < 1024)
        {
            if (null === $decimals)
            {
                $decimals = 0;
            }
            // Kilobytes.
            $formatted_size = number_format($size, $decimals, $decimal_separator, $thousand_separator) . ' B';
        }
        elseif ($size < 1048576)
        {
            if (null === $decimals)
            {
                $decimals = 0;
            }
            // Kylobytes.
            $formatted_size = number_format(($size / 1024), $decimals, $decimal_separator, $thousand_separator) . ' KB';
        }
        elseif ($size < 1073741824)
        {
            if (null === $decimals)
            {
                $decimals = 1;
            }
            // Megabytes.
            $formatted_size = number_format(($size / 1048576), $decimals, $decimal_separator, $thousand_separator) . ' MB';
        }
        else
        {
            if (null === $decimals)
            {
                $decimals = 1;
            }
            // Gigabytes.
            $formatted_size = number_format(($size / 1073741824), $decimals, $decimal_separator, $thousand_separator) . ' GB';
        }

        return $formatted_size;
    }
    );
}
