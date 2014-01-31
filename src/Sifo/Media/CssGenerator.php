<?php
/**
 * LICENSE
 *
 * Copyright 2010 Carlos Soriano
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

namespace Sifo\Media;

/**
 * CSS generator.
 */
class CssGenerator extends MediaGenerator
{
    /**
     * Store the current class instance.
     *
     * @var CssGenerator
     */
    static protected $instance;

    /**
     * Media type of the current generator.
     *
     * @var string
     */
    protected $media_type = 'css';

    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::setInstance();
    }

    /**
     * Class singleton.
     *
     * @return CssGenerator
     */
    public static function getInstance()
    {
        if ( isset( self::$instance ) )
        {
            return self::$instance;
        }

        return self::$instance = new self;
    }

    /**
     * Parse the media to add special content.
     *
     * @param string $content File content.
     * @return string
     */
    protected function parseContent( $content )
    {
        $content = str_replace( 'url(/images/', "url(/{$this->instance_language}/images/", $content );
        return $content;
    }
}