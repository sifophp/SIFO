<?php

function twig_filter_link_urls()
{
    return new \Twig_Filter(
        'link_urls', function (
        $string
    ) {
        return preg_replace_callback(
            "/\b(https?):\/\/([-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]*)\b/i",
            create_function(
                '$matches',
                'return "<a href=\'".($matches[0])."\'>".($matches[0])."</a>";'
            ),
            $string
        );
    }
    );
}
