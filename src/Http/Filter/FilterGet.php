<?php

namespace Sifo\Http\Filter;

class FilterGet extends Filter
{
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self ($_GET);
        }

        return self::$instance;
    }
}
