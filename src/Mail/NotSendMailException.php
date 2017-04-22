<?php

namespace Sifo\Mail;

class NotSendMailException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}
