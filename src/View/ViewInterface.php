<?php

namespace Sifo\View;

interface ViewInterface
{
    public function assign($variable_name, $value);

    public function fetch($template_path);
}
