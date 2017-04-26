<?php

namespace Sifo\Http\Filter;

class FilterCustom extends Filter
{
    public function __construct($request)
    {
        $this->request = $request;
    }

    public static function getInstance()
    {
        trigger_error('You shouldn\'t use Singleton for FilterCustom.', E_USER_ERROR);
    }
}
