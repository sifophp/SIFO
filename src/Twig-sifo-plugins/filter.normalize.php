<?php

function twig_filter_normalize()
{
    return new \Twig_Filter(
        'normalize', fn($string) => \Sifo\Urls::normalize($string)
    );
}
