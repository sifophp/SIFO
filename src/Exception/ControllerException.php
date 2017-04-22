<?php

namespace Sifo\Exception;

/**
 * @method SifoHttpException getPrevious()
 */
class ControllerException extends \Exception
{
    public function __construct($a_message = "", SifoHttpException $a_previous)
    {
        parent::__construct($a_message, 0, $a_previous);
    }
}
