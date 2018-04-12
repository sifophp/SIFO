<?php

namespace Sifo\Exception\Http;

class NotFound extends BaseException
{
    public function __construct(... $some_regular_exception_arguments)
    {
        parent::__construct(404, ... $some_regular_exception_arguments);
    }
}
