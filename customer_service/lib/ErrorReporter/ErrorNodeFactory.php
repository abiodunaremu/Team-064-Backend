<?php
namespace Lib\ErrorReporter;

use Lib\ErrorReporter\PersistenceError;
use Lib\ErrorReporter\ObjectError;

class ErrorNodeFactory
{
    public function createPersistenceError($errorMessage, $userRespone)
    {
        return new PersistenceError($errorMessage, $userRespone);
    }
    
    public function createObjectError($errorMessage, $userRespone)
    {
        return new ObjectError($errorMessage, $userRespone);
    }
}
