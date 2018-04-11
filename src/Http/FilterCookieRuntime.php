<?php

namespace Sifo\Http;

use Sifo\Http\Filter\FilterCookie;

/**
 * This class is used to update the runtime filter with the cookies changes.
 */
class FilterCookieRuntime extends FilterCookie
{
    public static function setCookie($key, $value)
    {
        FilterCookie::getInstance()->request[$key] = $value;
    }

    public static function deleteCookie($key)
    {
        unset(FilterCookie::getInstance()->request[$key]);
    }
}
