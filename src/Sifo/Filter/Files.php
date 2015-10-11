<?php
/**
 * LICENSE
 *
 * Copyright 2010 Albert Lombarte
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Sifo\Filter;

class Files extends Filter
{
    /**
     * Singleton object.
     *
     * @var Filter
     */
    protected static $instance;

    /**
     * Filters variables passed by File uploads.
     * @return Filter
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self($_FILES);
            $_FILES         = array();
        }

        return self::$instance;
    }

    /**
     * Get a variable without any type of filtering.
     *
     * @param string $var_name
     *
     * @return string
     */
    public function getUnfiltered($var_name)
    {
        $file = parent::getUnfiltered($var_name);

        if (UPLOAD_ERR_NO_FILE == $file['error']) {
            return false;
        }

        return $file;
    }
}
