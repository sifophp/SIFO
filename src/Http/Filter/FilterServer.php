<?php

namespace Sifo\Http\Filter;

class FilterServer extends Filter
{
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self ($_SERVER);
        }

        return self::$instance;
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
