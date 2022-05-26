<?php

declare(strict_types=1);

namespace Sifo\Test\Sifo;

use PHPUnit\Framework\TestCase;
use Sifo\Domains;

class DomainsTest extends TestCase
{
    private const DOMAIN_NAME = 'sifo.local';

    public function testGetInstanceFromDomainName()
    {
        $domain = Domains::getInstanceFromDomainName(self::DOMAIN_NAME);
        $this->assertSame(self::DOMAIN_NAME, $domain->getDomain());
    }
}
