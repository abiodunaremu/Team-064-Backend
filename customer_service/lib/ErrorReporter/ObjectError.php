<?php
namespace Lib\ErrorReporter;

use Lib\ErrorReporter\Errors;

class ObjectError extends Errors
{
    public function __construct($errorMessage, $userResponse)
    {
        $this->setErrorMessage($errorMessage);
        $this->setUserResponse($userResponse);
        $this->setErrorType("Object Error");
    }
}
