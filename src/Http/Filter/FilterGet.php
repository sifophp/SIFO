<?php

namespace Sifo\Http\Filter;

class FilterGet extends Filter
{
    public function __construct()
    {
        $this->request = $_GET;
    }
}
