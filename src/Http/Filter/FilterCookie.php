<?php

namespace Sifo\Http\Filter;

class FilterCookie extends Filter
{
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self ($_COOKIE);
        }

        return self::$instance;
    }
}
