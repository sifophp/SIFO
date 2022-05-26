<?php

declare(strict_types=1);

namespace Sifo\Test\Common;

use Psr\Container\ContainerInterface;
use Sifo\CLBootstrap;
use Sifo\Domains;
use Sifo\FilterServer;

class TestConsoleScriptRunner
{
    /** @var string */
    private $domainName;
    /** @var string */
    private $scriptFQCN;
    /** @var string */
    private $scriptLocation;
    /** @var array */
    private $argv;

    public function __construct(string $domainName, string $scriptFQCN, string $scriptLocation, array $argv)
    {
        $this->domainName = $domainName;
        $this->scriptFQCN = $scriptFQCN;
        $this->scriptLocation = $scriptLocation;
        $this->argv = $argv;
    }

    public function run(?ContainerInterface $container = null): string
    {
        Domains::getInstanceFromDomainName($this->domainName)->setDebugMode(false);
        $originalServer = $_SERVER;
        $_SERVER['PHP_SELF'] = $this->scriptLocation;
        $_SERVER['argv'] = $this->argv;
        $filter_server = FilterServer::getInstance();
        $filter_server->setVar('argv', $this->argv);

        ob_start();
        ob_start();
        CLBootstrap::$script_controller = $this->scriptFQCN;
        CLBootstrap::execute(null, null, $container);

        $output = ob_get_clean();

        $_SERVER = $originalServer;
        $filter_server->setVar('argv', $_SERVER['argv']);

        return $output;
    }
}
