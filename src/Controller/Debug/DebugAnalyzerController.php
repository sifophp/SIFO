<?php

namespace Sifo\Controller\Debug;

use Sifo\Controller\Controller;
use Sifo\Debug\DataBaseHandler;
use Sifo\Exception\Http\NotFound;
use Sifo\Http\Domains;
use Sifo\Http\Filter\FilterGet;

class DebugAnalyzerController extends Controller
{
    /**
     * @var DataBaseHandler Class responsible of dealing with stored debugs executions
     */
    private $debug_persistence_handler;

    /**
     * @var string Debug execution identifier. Obtained from the parsed param "execution_id".
     */
    private $execution_key;

    /**
     * @var array containing the debug data saved in DebugDataBaseDebugHandler (debug_content array and timestamp DateTime object)
     */
    private $debug_data;

    public function __construct()
    {
        parent::__construct();

        $this->debug_persistence_handler = new DataBaseHandler();
    }

    function build()
    {
        if (!Domains::getInstance()->getDevMode()) {
            throw new NotFound('Analyzer only available while in devel mode');
        }

        // Disables debug mode in order to do not save the debug for the Analyzer execution (avoid iinceeeptioooon)
        Domains::getInstance()->setDebugMode(false);

        // If the user has passed any execution identifier, show its debug
        if (!$this->execution_key = FilterGet::getInstance()->getString('execution_key')) {
            $this->execution_key = $this->debug_persistence_handler->getLastParentExecutionKey();
        }

        $this->getExecutionDebug();
    }

    private function getExecutionDebug()
    {
        if ($this->debug_data = $this->debug_persistence_handler->getExecutionDebugWithChildrenById($this->execution_key)) {
            $this->setLayout('debug/analyzer.tpl');

            $this->assign('debug_data', $this->debug_data);
            $this->assign('url', $this->getParam('url'));

            $this->assign('show_timers', true);
        } else {
            echo "No debug data found by this execution identifier.";
            exit;
        }
    }
}
