<?php

namespace Sifo\Http\Filter;

class FilterServer extends Filter
{
    public function __construct()
    {
        $this->request = $_SERVER;
    }

    /**
     * Mocks the host for use in scripts.
     *
     * @param string $mocked_host
     */
    public function setHost($mocked_host)
    {
        $this->request['HTTP_HOST'] = $mocked_host;
    }

}
