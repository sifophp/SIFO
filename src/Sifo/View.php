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

namespace Sifo;

/**
 * Templating engine. Compiles some smarty stuff for an easier management.
 */
class View
{
    /** @var string */
    private $template_path;

    /** @var ViewSmarty|ViewTwig */
    private $templating_engine;

    /** @var array */
    private $variables = [];

    public function fetch($template)
    {
        $this->template_path = $template;

        $this->chooseTemplatingEngine();
        $this->assignVariables();

        return $this->templating_engine->fetch($this->template_path);
    }

    public function assign($variable_name, $value)
    {
        $this->variables[$variable_name] = $value;
    }

    public function getTemplateVars()
    {
        return $this->variables;
    }

    private function assignVariables()
    {
        foreach ($this->variables as $variable => $value)
        {
            $this->templating_engine->assign($variable, $value);
        }
    }

    private function chooseTemplatingEngine()
    {
        $file_extension = pathinfo($this->template_path, PATHINFO_EXTENSION);

        if ('twig' != $file_extension)
        {
            $this->setSmartyTemplatingEngine();
        }
        else
        {
            $this->setTwigTemplatingEngine();
        }
    }

    private function setSmartyTemplatingEngine()
    {
        if ($this->templating_engine instanceof ViewSmarty)
        {
            return;
        }

        $this->templating_engine = new ViewSmarty();
    }

    private function setTwigTemplatingEngine()
    {
        if ($this->templating_engine instanceof ViewTwig)
        {
            return;
        }

        $this->templating_engine = new ViewTwig();
    }

    public static function customErrorHandler($errno, $errstr, $errfile, $errline)
    {
        $error_friendly = Debug::friendlyErrorType($errno);
        $error_string   = "[{$error_friendly}] {$errstr} in {$errfile}:{$errline}";

        if (Domains::getInstance()->getDebugMode())
        {
            Debug::subSet('smarty_errors', $errfile, '<pre>' . $error_string . '</pre>', true);
        }

        // Smarty only write PHP USER errors to log:
        if (($raw_url = Urls::$actual_url))
        {
            error_log("URL '{$raw_url}' launched the following Smarty error: {$error_string}");

            return true;
        }

        // Follow the error handling flow:
        return false;
    }
}
