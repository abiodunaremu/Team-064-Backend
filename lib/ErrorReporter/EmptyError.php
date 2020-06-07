<?php
namespace Lib\ErrorReporter;

use Lib\ErrorReporter\Errors;

class EmptyError extends Errors
{
    public function __construct()
    {
        $this->setErrorMessage("No error yet");
        $this->setErrorType("Empty");
    }
}
