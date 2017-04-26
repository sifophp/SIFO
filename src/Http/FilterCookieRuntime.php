<?php

namespace Sifo\Http;

use Sifo\Http\Filter\FilterCookie;

/**
 * This class is used to update the runtime filter with the cookies changes.
 *
 * @author Albert Lombarte, Sergio Ambel
 */
class FilterCookieRuntime extends FilterCookie
{

    static public function setCookie($key, $value)
    {
        self::getInstance()->request[$key] = $value;
    }

    static public function deleteCookie($key)
    {
        unset(self::getInstance()->request[$key]);
    }
}
