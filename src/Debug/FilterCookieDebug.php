<?php

namespace Sifo\Debug;

use Sifo\Http\Filter\FilterCookie;

class FilterCookieDebug extends FilterCookie
{

    public static function getCookiesArray()
    {
        $all_cookies = (self::getInstance()->request);

        $uncommon_cookies = array();
        foreach ($all_cookies as $key => $value) {
            if (preg_match("/^[^__]/", $key)) {
                $uncommon_cookies[$key] = $value;
            }
        }

        return (isset($uncommon_cookies) && count($uncommon_cookies)) ? $uncommon_cookies : null;
    }
}
