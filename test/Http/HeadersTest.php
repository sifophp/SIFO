<?php

namespace Sifo\Http;

use PHPUnit\Framework\TestCase;
use Sifo\Exception\HeadersException;

class HeadersTest extends TestCase
{
    /** @test */
    public function shouldBeAbleToSetDefaultHeaders()
    {
        $this->whenHeadersTryToSetDefaultHeaders();
        $this->thenDefaultHeadersShouldBeSet();
    }

    /** @test */
    public function shouldBeAbleToSetAValidHttpCode()
    {
        Headers::setResponseStatus(200);
        $ok_headers = $this->getOkHeaders();
        $current_headers = Headers::getAll();
        $this->assertTrue($this->foundHeaderInsideHeaders($current_headers, $ok_headers));
    }

    /** @test */
    public function shouldNotBeAbleToSetAnInvalidHttpCode()
    {
        $this->expectException(HeadersException::class);
        Headers::setResponseStatus(2000);
    }

    /** @test */
    public function shouldSaveHeadersToHistoryAfterSendingThem()
    {
        @Headers::send();
        $history_headers = Headers::getDebugInfo()[0];

        $default_headers = $this->getDefaultHeaders();
        $this->assertTrue($this->foundHeaderInsideHeaders($history_headers, $default_headers));

        $ok_headers = $this->getOkHeaders();
        $this->assertTrue($this->foundHeaderInsideHeaders($history_headers, $ok_headers));
    }

    private function whenHeadersTryToSetDefaultHeaders()
    {
        Headers::setDefaultHeaders();
    }

    private function thenDefaultHeadersShouldBeSet()
    {
        $default_headers = $this->getDefaultHeaders();
        $current_headers = Headers::getAll();
        $this->assertTrue($this->foundHeaderInsideHeaders($current_headers, $default_headers));
    }

    private function foundHeaderInsideHeaders(array $current_headers, array $header)
    {
        return in_array($header, $current_headers);
    }

    private function getDefaultHeaders()
    {
        return [
            "content" => "Content-Type: text/html; charset=UTF-8",
            "replace" => true,
            "http_code" => false
        ];
    }

    private function getOkHeaders()
    {
        return [
            "content" => "HTTP/1.0 200 OK",
            "replace" => true,
            "http_code" => false
        ];
    }
}
