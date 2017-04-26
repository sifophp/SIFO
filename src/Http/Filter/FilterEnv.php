<?php

namespace Sifo\Http\Filter;

class FilterEnv extends Filter
{
    public function __construct()
    {
        $this->request = $_ENV;
    }
}
