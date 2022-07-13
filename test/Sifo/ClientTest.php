<?php

namespace Sifo\Test\Sifo;

use Sifo\Client;
use PHPUnit\Framework\TestCase;
use Sifo\Registry;

class ClientTest extends TestCase
{
    public function testGetBrowser(): void
    {
        $browser = Client::getBrowser();

        $this->assertSame('unknown', $browser->Device_Name);
        $this->assertSame('unknown', $browser->Platform);
        $this->assertSame('unknown', $browser->Platform_Version);
        $this->assertSame('0.0', $browser->Version);
        $this->assertSame(false, $browser->isMobileDevice);
        $this->assertSame(false, $browser->Crawler);
        $this->assertSame('', $browser->browser_name);
        $this->assertSame($browser, Registry::get('Client_Browser'));
    }
}
