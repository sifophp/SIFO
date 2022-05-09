<?php

namespace Sifo\Test\Sifo;

use PHPUnit\Framework\TestCase;
use Sifo\FilterCookieRuntime;

class CookieTest extends TestCase
{
    protected function setUp()
    {
        TestCookie::clearCookies();
    }

    public function testCookieIsSetWithExpectedDefaults(): void
    {
        $cookieName = 'test_cookie';
        $defaultExpiration = time() + ( 86400 * 14 );

        TestCookie::set($cookieName, 'oreo');

        $this->assertEquals(
            $defaultExpiration,
            TestCookie::getExpires($cookieName),
            "Expires doesn't match with expected."
        );
        $this->assertSame(
            'sifo.local',
            TestCookie::getDomain($cookieName),
            "Domain doesn't match with expected."
        );
        $this->assertSame(
            '/',
            TestCookie::getPath($cookieName),
            "Path doesn't match with expected."
        );
        $this->assertNull(TestCookie::getSameSite($cookieName));
        $this->assertFalse(TestCookie::isSecure($cookieName));
        $this->assertFalse(TestCookie::isHttpOnly($cookieName));
    }

    public function testCookieCanBeSetWithoutExpiration(): void
    {
        $cookieName = 'test_cookie';

        TestCookie::set($cookieName, 'oreo', 0);

        $this->assertSame(0, TestCookie::getExpires($cookieName));
    }

    public function testCookieIsSetWithCustomParameters(): void
    {
        $cookieName = 'test_cookie';
        $expirationDays = 7;
        $defaultExpiration = time() + ( 86400 * $expirationDays );

        TestCookie::set($cookieName, 'chips_ahoy', $expirationDays, false, true, true, 'Lax');

        $this->assertEquals(
            $defaultExpiration,
            TestCookie::getExpires($cookieName),
            "Expires doesn't match with expected."
        );
        $this->assertSame(
            'sifo.local',
            TestCookie::getDomain($cookieName),
            "Domain doesn't match with expected."
        );
        $this->assertSame(
            '/',
            TestCookie::getPath($cookieName),
            "Path doesn't match with expected."
        );
        $this->assertSame(
            'Lax',
            TestCookie::getSameSite($cookieName),
            "Same site doesn't match with expected."
        );
        $this->assertTrue(TestCookie::isSecure($cookieName));
        $this->assertTrue(TestCookie::isHttpOnly($cookieName));
    }

    public function testCookieValueIsStoredInFilterCookie(): void
    {
        $cookieName = 'test_cookie';

        TestCookie::set($cookieName, 'oreo');

        $this->assertSame(
            'oreo',
            FilterCookieRuntime::getInstance()->getString('test_cookie')
        );
    }

    public function testCookieValueIsDeletedFromFilterCookie(): void
    {
        $cookieName = 'test_cookie';

        TestCookie::set($cookieName, 'oreo');
        TestCookie::delete($cookieName);

        $this->assertTrue(FilterCookieRuntime::getInstance()->isEmpty('test_cookie'));
    }
}
