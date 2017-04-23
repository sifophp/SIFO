<?php

namespace Sifo\Http;

use Sifo\Exception\HeadersException;

/**
 * Stack of HTTP headers to be sent to the browser just before the output.
 *
 * Use "set" method to add headers to the stack.
 * Use "setResponseStatus" for HTTP codes only.
 * Use "send" to send all the headers to the browser.
 *
 * Examples:
 *
 * // Set several headers with the same key ('replace' parameter at the end set to false)
 * Headers::set( 'WWW-Authenticate', 'Negotiate' )
 * Headers::set( 'WWW-Authenticate', 'NTLM', false )
 *
 * // Will send:
 * WWW-Authenticate: Negotiate
 * WWW-Authenticate: NTLM
 *
 * Headers::set( 'Content-Type', 'application/pdf' )
 * Headers::set( 'Content-Type', 'application/json' )
 *
 * // Will send:
 * Content-Type: application/json
 * (pdf is ignored because the "replace" is true by default)
 *
 * The headers won't be sent until you execute:
 * Headers::send();
 */
class Headers
{
    /**
     * Headers that are status codes. E.g: "HTTP/1.0 404 Not Found"
     */
    const FORMAT_TYPE_STATUS = 'HTTP/1.0 %s %s';

    /**
     * Headers made of a key and a value. E.g: "WWW-Authenticate: Negotiate"
     */
    const FORMAT_KEY_VALUE = '%s: %s';

    /**
     * List of all the headers sent by the application so far.
     *
     * @var array
     */
    protected static $headers = [];

    /**
     * Headers history.
     *
     * @var array
     */
    protected static $history = [];

    /**
     * Known HTTP codes by this framework.
     */
    public static $http_codes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '( Unused )',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    ];

    public static function setDefaultHeaders()
    {
        self::set('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Creates a new header with the key and values passed.
     *
     * @param string $key The header name (e.g: Content-Type)
     * @param string $value The value for the header (e.g: application/json)
     * @param int|null $http_code
     * @param bool $replace Adds an additional value to any existing key.
     */
    public static function set(string $key, string $value, int $http_code = null, bool $replace = true)
    {
        self::pushHeader($key, $value, self::FORMAT_KEY_VALUE, $http_code, $replace);
    }

    /**
     * Creates the status header with the HTTP code that will be sent to the client.
     *
     * @param int $http_code Http status code (e.g: 404)
     * @throws HeadersException
     */
    public static function setResponseStatus(int $http_code)
    {
        if (isset(self::$http_codes[$http_code])) {
            $msg = self::$http_codes[$http_code];
            self::pushHeader($http_code, $msg, self::FORMAT_TYPE_STATUS);
        } else {
            throw new HeadersException("Unknown status code requested $http_code");
        }
    }

    /**
     * It formats the header and adds it to the stack.
     *
     * @param string $key Header name
     * @param string $value Header value
     * @param string $format The sprintf format used to format the content.
     * @param int $http_code Additional set of HTTP status code with the header. Suitable for "Location" header.
     * @param boolean $replace If the header overwrites any similar existing header.
     */
    protected static function pushHeader(
        string $key,
        string $value,
        string $format,
        int $http_code = null,
        bool $replace = true
    ) {
        $header = [
            'content' => sprintf($format, $key, $value),
            'replace' => $replace,
            'http_code' => $http_code ?: false
        ];

        array_push(self::$headers, $header);
    }

    public static function get($key)
    {
        if (!isset(self::$headers[$key])) {
            return false;
        }

        return self::$headers[$key] ?: false;
    }

    public static function getAll()
    {
        return self::$headers;
    }

    /**
     * Sends all the headers to the browser.
     */
    public static function send()
    {
        foreach (self::$headers as $header => $values) {
            if ($values['http_code']) {
                header($values['content'], $values['replace'], $values['http_code']);
            } else {
                header($values['content'], $values['replace']);
            }
        }

        // Clear the stack after writing:
        self::$history[] = self::$headers;
        self::$headers = [];
    }

    /**
     * Returns all the blocks of headers written so far.
     *
     * @return array
     */
    public static function getDebugInfo()
    {
        return self::$history;
    }
}
