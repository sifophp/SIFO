<?php

namespace Sifo\Exception\Http;

final class TemporalRedirect extends Redirect
{
    public function __construct(string $url)
    {
        parent::__construct(302, $url);
    }
}
