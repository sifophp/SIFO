<?php

function twig_filter_normalize()
{
    return new \Twig_Filter(
        'normalize', function (
        $string
    ) {
        return \Sifo\Urls::normalize($string);
    }
    );
}
