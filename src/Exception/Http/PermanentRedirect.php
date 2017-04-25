<?php

namespace Sifo\Exception\Http;

final class PermanentRedirect extends Redirect
{
    public function __construct(... $some_regular_exception_arguments)
    {
        parent::__construct(301, ... $some_regular_exception_arguments);
    }
}
