<?php

/**
 * LICENSE.
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
 */
namespace Sifo\Filter;

use Sifo\Exception\FilterException;

final class Custom extends Filter
{
    /**
     * Allow creation of different objects, the Filter\Custom is not based on
     * global values like $_GET or $_POST and might be used for different purposes
     * in the same execution thread.
     *
     * @param array $request
     *
     * @return Custom
     */
    public function __construct($request)
    {
        return parent::__construct($request);
    }

    public static function getInstance()
    {
        throw new FilterException('You shouldn\'t use getInstance to build custom filter. Use a "new Custom([...])" statement instead.');
    }
}
