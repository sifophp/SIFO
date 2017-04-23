<?php

namespace Sifo\View;

use Sifo\Bootstrap;
use Sifo\I18N;

class Twig implements ViewInterface
{
    /** @var \Twig_Environment */
    private $twig;

    /** @var array */
    private $variables = [];

    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem(ROOT_PATH);
        $this->twig = new \Twig_Environment(
            $loader, [
                'autoescape' => false,
                'cache' => ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/_smarty/compile/'
            ]
        );

        $this->twig->addExtension(new \Twig_Extensions_Extension_Text());

        $function = new \Twig_SimpleFunction(
            't', function ($text) {
            return I18N::getTranslation($text);
        }
        );

        $this->twig->addFunction($function);
    }

    public function assign($variable_name, $value)
    {
        $this->variables[$variable_name] = $value;
    }

    public function fetch($template_path)
    {
        set_error_handler(array(Views::class, "customErrorHandler"));

        $template_path = str_replace(ROOT_PATH, '', $template_path);

        try {
            $result = $this->twig->render($template_path, $this->variables);
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $result = null;
        }

        restore_error_handler();

        return $result;
    }
}
