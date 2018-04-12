<?php

namespace Sifo\Exception\Http;

class Unauthorized extends BaseException
{
    public function __construct(... $some_regular_exception_arguments)
    {
        parent::__construct(401, ... $some_regular_exception_arguments);
    }
}
