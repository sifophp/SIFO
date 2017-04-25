<?php

namespace Sifo\Exception\Http;

final class TemporalRedirect extends Redirect
{
    public function __construct(... $some_regular_exception_arguments)
    {
        parent::__construct(302, ... $some_regular_exception_arguments);
    }
}
