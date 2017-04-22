<?php
namespace Sifo\Exception;

use Sifo\Http\Headers;

class SifoException extends \Exception
{
    /** @var integer */
    public $http_code = 302;

    /** @var string */
    public $http_code_msg = '';

    /** @var boolean */
    public $redirect = false;

    public function __construct(int $http_code, ... $regular_exception_arguments)
    {
        // Invoke parent to ensure all available data has been properly assigned:
        parent::__construct(... $regular_exception_arguments);

        $current_exception      = get_class($this);
        $current_exception_code = ( int ) str_replace(__NAMESPACE__ . '\\Exception_', '', $current_exception);

        // See if the http status code needs a redirection:
        if ((300 <= $current_exception_code) && (307 >= $current_exception_code))
        {
            $this->redirect = true;
        }

        if (isset(Headers::$http_codes[$current_exception_code]))
        {
            $this->http_code     = $current_exception_code;
            $this->http_code_msg = Headers::$http_codes[$current_exception_code];
        }
        else
        {
            // The passed exception is not in the list. Pass a 500 error.
            $this->http_code     = 500;
            $this->http_code_msg = 'Internal Server Error';
        }

        // Set internal exception vars if they are empty (non declared in constructor).
        // This allows usage of methods as $e->getMessage() or $e->getCode()
        if (0 == $this->code)
        {
            $this->code = $this->http_code;
        }

        if (null === $this->message)
        {
            $this->message = $this->http_code_msg;
        }
    }

    public static function __callStatic($http_code, $regular_eregular_exception_arguments)
    {
        return new self((int) $http_code, ... $regular_eregular_exception_arguments);
    }

    public static function raise($message, $code)
    {
        if (isset(Headers::$http_codes[$code]))
        {
            $exception = forward_static_call([self::class, $code], $message);
            throw $exception;
        }
        else
        {
            throw SifoException::CODE_500($message, $code);
        }
    }
}
