<?php

namespace Sifo\Http\Filter;

class FilterFiles extends Filter
{
    public function __construct()
    {
        $this->request = $_FILES;
    }

    public function getUnfiltered(string $var_name)
    {
        $file = parent::getUnfiltered($var_name);

        if (UPLOAD_ERR_NO_FILE === $file['error']) {
            return false;
        }

        return $file;
    }

}
