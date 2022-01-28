<?php

namespace Sifo;

use PHPUnit\Framework\TestCase;

class I18NTest extends TestCase
{
    /** @var false|string */
    private $rootDir;

    protected function setUp(): void
    {
        $this->rootDir = realpath(dir('.')->path);
        if (false === defined('ROOT_PATH')) {
            define('ROOT_PATH', $this->rootDir);
        }
        Bootstrap::$instance = 'example';
        I18N::setDomain('messages', 'en_US', 'example');
    }

    public function testGetExistingTranslation(): void
    {
        I18N::getInstance('messages', 'en_US');
        $translation = I18N::getTranslation('%1 is in da house, kind\'a funny uh?', ['%1' => 'koldo']);

        $this->assertSame('The developer koldo is behind the scenes, serious work on progress...', $translation);
    }
}
