<?php

namespace Sifo\Http\Filter;

class FilterFiles extends Filter
{
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self ($_FILES);
            $_FILES = array();
        }

        return self::$instance;
    }

    public function getUnfiltered(string $var_name)
    {
        $file = parent::getUnfiltered($var_name);

        if (UPLOAD_ERR_NO_FILE == $file['error']) {
            return false;
        }

        return $file;
    }

}
