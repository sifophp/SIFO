<?php

namespace Sifo\Exception\Http;

final class Forbidden extends BaseException
{
    public function __construct(... $some_regular_exception_arguments)
    {
        parent::__construct(403, ... $some_regular_exception_arguments);
    }
}
