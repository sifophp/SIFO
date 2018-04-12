<?php

namespace Sifo\Exception\Http;

class InternalServerError extends BaseException
{
    public function __construct(... $some_regular_exception_arguments)
    {
        parent::__construct(500, ... $some_regular_exception_arguments);
    }
}
