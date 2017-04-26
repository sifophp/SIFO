<?php

namespace Sifo\Http\Filter;

class FilterRequest extends Filter
{
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self ($_REQUEST);
        }

        return self::$instance;
    }
}
