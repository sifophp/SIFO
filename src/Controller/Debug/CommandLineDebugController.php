<?php

namespace Sifo\Controller\Debug;

use Sifo\CLBootstrap;
use Sifo\Http\Urls;

class CommandLineDebugController extends DebugController
{
    /**
     * Override method in order to store the executed command as the execution URL in the debug DB.
     *
     * @return string with the executed command.
     */
    protected function getDebugUrl()
    {
        return implode(' ', CLBootstrap::getCommandLineParams());
    }

    /**
     * Override method in order to show a message with a link to the Sifo Debug Analyzer.
     *
     * @param array $debug_data Structure containing all debug related data.
     */
    protected function finalRender($debug_data)
    {
        parent::finalRender($debug_data);

        $url = Urls::getInstance()->getUrlConfig();
        echo '[INFO] Script debug properly saved. You can check it out at: ' . $url['sifo_debug_analyzer'] . '?execution_key=' . \Sifo\Debug::getExecutionKey() . PHP_EOL;
    }
}
