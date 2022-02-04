<?php

namespace Sifo\Test\Sifo;

use PHPUnit\Framework\TestCase;
use Sifo\I18N;

class I18NTest extends TestCase
{
    protected function setUp(): void
    {
        I18N::setDomain('messages', 'en_US', 'example');
    }

    public function testGetExistingTranslation(): void
    {
        I18N::getInstance('messages', 'en_US');
        $translation = I18N::getTranslation('%1 is in da house, kind\'a funny uh?', ['%1' => 'koldo']);

        $this->assertSame('The developer koldo is behind the scenes, serious work on progress...', $translation);
    }
}
