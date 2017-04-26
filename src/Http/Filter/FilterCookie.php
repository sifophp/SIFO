<?php

namespace Sifo\Http\Filter;

class FilterCookie extends Filter
{
    public function __construct()
    {
        $this->request = $_COOKIE;
    }
}
