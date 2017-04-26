<?php

namespace Sifo\Http\Filter;

class FilterPost extends Filter
{
    public function __construct()
    {
        $this->request = $_POST;
    }
}
