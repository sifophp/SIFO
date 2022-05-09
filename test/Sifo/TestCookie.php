<?php

namespace Sifo\Test\Sifo;

use InvalidArgumentException;
use Sifo\Cookie;
use Sifo\Domains;

class TestCookie extends Cookie
{
    protected static function setCookie(
        string $name,
        $value = "",
        $expires = 0,
        $path = "",
        $domain = "",
        $secure = false,
        $httponly = false,
        string $samesite = null
    ): bool {
        if ($value === "") {
            unset(self::$cookies[$name]);

            return true;
        }

        self::$cookies[$name] = [
            'value' => $value,
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ];

        return true;
    }

    public static function getCookieParam(string $name, string $param)
    {
        if (!array_key_exists($name, self::$cookies)) {
            throw new InvalidArgumentException("Cookie of name $name is not set.");
        }

        if (!array_key_exists($param, self::$cookies[$name] ?? [])) {
            return null;
        }

        return self::$cookies[$name][$param];
    }

    public static function getValue(string $name): string
    {
        return (string) self::getCookieParam($name, 'value');
    }

    public static function getExpires(string $name)
    {
        return self::getCookieParam($name, 'expires');
    }

    public static function getPath(string $name): string
    {
        return (string) self::getCookieParam($name, 'path');
    }

    public static function getDomain(string $name): string
    {
        return (string) self::getCookieParam($name, 'domain');
    }

    public static function isSecure(string $name): bool
    {
        return (bool) self::getCookieParam($name, 'secure');
    }

    public static function isHttpOnly(string $name): bool
    {
        return (bool) self::getCookieParam($name, 'httponly');
    }

    public static function getSameSite(string $name): ?string
    {
        return self::getCookieParam($name, 'samesite');
    }

    public static function clearCookies(): void
    {
        self::$cookies = [];
    }
}
