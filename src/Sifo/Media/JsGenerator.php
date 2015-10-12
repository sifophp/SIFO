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

namespace Sifo\Media;

use Sifo\Media\MediaGenerator;

/**
 * Javascript generator.
 */
class JsGenerator extends MediaGenerator
{
    /**
     * Store the current class instance.
     *
     * @var JsGenerator
     */
    static protected $instance;

    /**
     * Media type of the current generator.
     *
     * @var string
     */
    protected $media_type = 'js';

    /**
     * Generates all groups, and not only the ones added by the controller.
     *
     * @var boolean
     */
    protected $generate_all_groups = true;

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
     * @return JsGenerator
     */
    public static function getInstance()
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }

        return self::$instance = new self;
    }

    /**
     * Gets the code that will be embeeded in the head JS file.
     *
     * This code relates module names to the generated js file that contains the code.
     *
     * @param array $media_list      The list of media files that has been generated.
     * @param array $generated_files The resultant generated files.
     *
     * @return string
     */
    protected function getBaseCode(Array $media_list, Array $generated_files)
    {
        if (count($media_list) !== count($generated_files)) {
            trigger_error('The number of groups does not match the number of files generated!', E_USER_WARNING);
        }
        $base_code = <<<CODE
var basePathConfig = {
CODE;
        foreach ($media_list as $group => $media_data) {
            $generated_file = array_shift($generated_files);
            $base_array[]   = "'$group': '$this->instance_static_host/$generated_file'";
        }

        $base_code .= implode(',', $base_array);
        $base_code .= '};' . "\n";

        return $base_code;
    }
}
