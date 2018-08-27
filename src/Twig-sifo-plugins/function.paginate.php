<?php

function twig_function_paginate($twig)
{
    return new \Twig_Function(
        'paginate', function (array $args = []) use ($twig) {
        $sOut = null;
        if (isset($args[0]['data']['template']) && !empty($args[0]['data']['template']))
        {
            $sOut = $twig->fetch($args[0]['data']['template']);
        }

        return $sOut;
    }, ['is_variadic' => true]
    );
}
