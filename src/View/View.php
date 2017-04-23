<?php

namespace Sifo\View;

use Sifo\Debug\Debug;
use Sifo\Http\Domains;
use Sifo\Http\Urls;

/**
 * Templating engine. Compiles some smarty stuff for an easier management.
 */
class Views
{
    /** @var string */
    private $template_path;

    /** @var Smarty|Twig */
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
        foreach ($this->variables as $variable => $value) {
            $this->templating_engine->assign($variable, $value);
        }
    }

    private function chooseTemplatingEngine()
    {
        $file_extension = pathinfo($this->template_path, PATHINFO_EXTENSION);

        if ('twig' != $file_extension) {
            $this->setSmartyTemplatingEngine();
        } else {
            $this->setTwigTemplatingEngine();
        }
    }

    private function setSmartyTemplatingEngine()
    {
        if ($this->templating_engine instanceof Smarty) {
            return;
        }

        $this->templating_engine = new Smarty();
    }

    private function setTwigTemplatingEngine()
    {
        if ($this->templating_engine instanceof Twig) {
            return;
        }

        $this->templating_engine = new Twig();
    }

    public static function customErrorHandler($errno, $errstr, $errfile, $errline)
    {
        $error_friendly = Debug::friendlyErrorType($errno);
        $error_string = "[{$error_friendly}] {$errstr} in {$errfile}:{$errline}";

        if (Domains::getInstance()->getDebugMode()) {
            Debug::subSet('smarty_errors', $errfile, '<pre>' . $error_string . '</pre>', true);
        }

        // Smarty only write PHP USER errors to log:
        if (($raw_url = Urls::$actual_url)) {
            error_log("URL '{$raw_url}' launched the following Smarty error: {$error_string}");

            return true;
        }

        // Follow the error handling flow:
        return false;
    }
}
