<?php

function twig_filter_link_urls()
{
    return new \Twig_Filter(
        'link_urls', fn($string) => preg_replace_callback(
            "/\b(https?):\/\/([-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]*)\b/i",
            static fn($matches) => "<a href=\'".($matches[0])."\'>".($matches[0])."</a>",
            $string
        )
    );
}
