<?php

function twig_function_t()
{
    return new \Twig_Function(
        't', fn($text) => \Sifo\I18N::getTranslation($text)
    );
}
