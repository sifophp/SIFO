<?php

namespace Sifo\Http\Filter;

class FilterRequest extends Filter
{
    public function __construct()
    {
        $this->request = $_REQUEST;
    }
}
