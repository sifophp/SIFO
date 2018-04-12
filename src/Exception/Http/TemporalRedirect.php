<?php

namespace Sifo\Exception\Http;

class TemporalRedirect extends Redirect
{
    public function __construct(string $url)
    {
        parent::__construct(302, $url);
    }
}
