<?php

namespace Sifo\Http\Filter;

class FilterEnv extends Filter
{
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self ($_ENV);
            $_ENV = array();
        }

        return self::$instance;
    }
}
