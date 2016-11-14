<?php

namespace Sifo;

class ViewTwig implements ViewInterface
{
    /** @var \Twig_Environment */
    private $twig;

    /** @var array */
    private $variables = [];

    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem('/');

        $templates_path = ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/';
        $this->twig     = new \Twig_Environment(
            $loader, [
                'autoescape' => false,
                'cache'      => $templates_path . '_smarty/compile/'
            ]
        );

        $this->twig->addExtension(new \Twig_Extensions_Extension_Text());

        $function = new \Twig_SimpleFunction('t', function ($text) {
            return I18N::getTranslation($text);
        });

        $this->twig->addFunction($function);

    }

    public function assign($variable_name, $value)
    {
        $this->variables[$variable_name] = $value;
    }

    public function fetch($template_path)
    {
        set_error_handler(array(View::class, "customErrorHandler"));

        try
        {
            $result = $this->twig->render($template_path, $this->variables);
        }
        catch (\Exception $e)
        {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $result = null;
        }

        restore_error_handler();

        return $result;
    }
}
