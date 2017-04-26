<?php

namespace Sifo\Http\Filter;

class FilterSession extends Filter
{
    public function __construct()
    {
        $this->request = $_SESSION;
    }
}
