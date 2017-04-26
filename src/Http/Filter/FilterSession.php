<?php

namespace Sifo\Http\Filter;

class FilterSession extends Filter
{
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self ($_SESSION);
        }

        return self::$instance;
    }
}
