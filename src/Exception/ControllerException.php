<?php

namespace Sifo\Exception;

use Sifo\Exception\Http\BaseException;

/**
 * @method BaseException getPrevious()
 */
class ControllerException extends \Exception
{
    public function __construct($a_message = "", BaseException $a_previous)
    {
        parent::__construct($a_message, 0, $a_previous);
    }
}
