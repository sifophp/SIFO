<?php

namespace Sifo\Exception\Http;

class PermanentRedirect extends Redirect
{
    public function __construct(string $url)
    {
        parent::__construct(301, $url);
    }
}
