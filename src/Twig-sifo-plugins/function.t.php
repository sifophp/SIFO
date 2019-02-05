<?php

function twig_function_t()
{
    return new \Twig_Function(
        't', function ($text) {
        return \Sifo\I18N::getTranslation($text);
    }
    );
}
