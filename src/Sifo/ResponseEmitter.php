<?php

declare(strict_types=1);

namespace Sifo;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ResponseEmitter
{
    public function emit(ResponseInterface $response) : bool
    {
        $this->assertNoPreviousOutput();
        $this->emitHeaders($response);
        $this->emitStatusLine($response);
        $this->emitBody($response);
        return true;
    }

    private function emitBody(ResponseInterface $response) : void
    {
        echo $response->getBody();
    }

    private function assertNoPreviousOutput()
    {
        if (headers_sent()) {
            throw new RuntimeException('Headers already sent.');
//            throw EmitterException::forHeadersSent();
        }
        if (ob_get_level() > 0 && ob_get_length() > 0) {
            throw new RuntimeException('Output already sent.');
//            throw EmitterException::forOutputSent();
        }
    }

    private function emitStatusLine(ResponseInterface $response) : void
    {
        $reasonPhrase = $response->getReasonPhrase();
        $statusCode   = $response->getStatusCode();
        header(sprintf(
            'HTTP/%s %d%s',
            $response->getProtocolVersion(),
            $statusCode,
            ($reasonPhrase ? ' ' . $reasonPhrase : '')
        ), true, $statusCode);
    }

    private function emitHeaders(ResponseInterface $response) : void
    {
        $statusCode = $response->getStatusCode();
        foreach ($response->getHeaders() as $header => $values) {
            $name  = $this->filterHeader($header);
            $first = $name === 'Set-Cookie' ? false : true;
            foreach ($values as $value) {
                header(sprintf(
                    '%s: %s',
                    $name,
                    $value
                ), $first, $statusCode);
                $first = false;
            }
        }
    }

    private function filterHeader(string $header) : string
    {
        return ucwords($header, '-');
    }
}
