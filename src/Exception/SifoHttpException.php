<?php

namespace Sifo\Exception;

use Sifo\Http\Headers;
use Sifo\Http\Urls;

class SifoHttpException extends \Exception
{
    /** @var integer */
    private $http_code;

    /** @var string */
    public $http_code_msg;

    public function __construct(int $http_code, ... $regular_exception_arguments)
    {
        parent::__construct(... $regular_exception_arguments);

        $this->validateHttpCode($http_code);
        $this->setInternalDefaultPropertiesValue();
    }

    public static function PermanentRedirect(... $regular_regular_exception_arguments)
    {
        return new self(301, ... $regular_regular_exception_arguments);
    }

    public static function TemporalRedirect(... $regular_regular_exception_arguments)
    {
        return new self(302, ... $regular_regular_exception_arguments);
    }

    public static function NotAuthorized(... $regular_regular_exception_arguments)
    {
        return new self(401, ... $regular_regular_exception_arguments);
    }

    public static function NotFound(... $regular_regular_exception_arguments)
    {
        return new self(404, ... $regular_regular_exception_arguments);
    }

    public static function InternalServerError(... $regular_regular_exception_arguments)
    {
        return new self(500, ... $regular_regular_exception_arguments);
    }

    public function isRedirect()
    {
        return 300 <= $this->http_code && 307 >= $this->http_code;
    }

    public function getHttpCode()
    {
        return $this->http_code;
    }

    public function getHttpCodeMessage()
    {
        return $this->http_code_msg;
    }

    private function setInternalDefaultPropertiesValue()
    {
        if (empty($this->code))
        {
            $this->code = $this->http_code;
        }

        if (empty($this->message))
        {
            $this->message = $this->http_code_msg;
        }
    }

    private function validateHttpCode(int $http_code)
    {
        if (!$this->existsHttpCode($http_code))
        {
            $this->http_code     = 500;
            $this->http_code_msg = 'Internal Server Error';

            return;
        }

        if ($this->isRedirect() && !$this->hasBeenProvidedAValidRedirectLocation())
        {
            trigger_error("Exception " . $this->http_code . " raised with an empty or invalid location (" . $this->message . ") " . $this->getTraceAsString(), E_ERROR);
            Headers::setResponseStatus(500);
            Headers::send();
            exit;
        }

        $this->http_code     = $http_code;
        $this->http_code_msg = Headers::$http_codes[$http_code];
    }

    public function getRedirectLocation()
    {
        if (!$this->isRedirect())
        {
            throw new \Exception('You can\'t recover location from a non-redirect http exception.');
        }

        $path = trim($this->http_code_msg, '/');

        if (false !== strpos($path, '://'))
        {
            return $path;
        }

        return Urls::getUrl($path);
    }

    private function existsHttpCode(int $http_code): bool
    {
        return isset(Headers::$http_codes[$http_code]);
    }

    private function hasBeenProvidedAValidRedirectLocation(): bool
    {
        return filter_var($this->message, FILTER_VALIDATE_URL);
    }
}
