<?php

function twig_filter_search_links()
{
    return new \Twig_Filter(
        'search_links', function (
        $text,
        $title = '',
        $rel = 'nofollow'
    ) {
        preg_match_all('/(http|ftp)+(s)?:(\/\/)((\w|\.)+)(\/)?(\S+)?(\.*)?/i', $text, $matches);
        if (!empty($matches[0]))
        {
            array_unique($matches[0]);
            foreach ($matches[0] as $url)
            {
                $text = str_replace($url, "<a title=\"$title\" href=\"$url\" rel=\"$rel\" class=\"url\">$url</a>", $text);
            }
        }

        return $text;
    }
    );
}
