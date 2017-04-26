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
        trigger_error('You shouldn\'t use Singleton for FilterCustom to prevent sideefects of this pattern with custom variables.',
            E_USER_ERROR);
    }
}
