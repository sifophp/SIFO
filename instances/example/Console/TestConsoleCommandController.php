<?php

declare(strict_types=1);

namespace Sifo\Example\Console;

use Common\SharedCommandLineController;

class TestConsoleCommandController extends SharedCommandLineController
{
    function init()
    {
    }

    function exec()
    {
        echo 'Hello World!';
    }
}
