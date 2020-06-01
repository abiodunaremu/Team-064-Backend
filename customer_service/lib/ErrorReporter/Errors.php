<?php
namespace Lib\ErrorReporter;

use Lib\ErrorReporter\ErrorNode;

class Errors implements ErrorNode
{
    public $source;
    public $previousNode;
    public $nextNode;
    public $errorMessage;
    public $errorType;
    public $userResponse;

    public function setPreviousNode($errorNode)
    {
        $this->previousNode = $errorNode;
    }

    public function getPreviousNode()
    {
        return $this->previousNode;
    }

    public function setNextNode($errorNode)
    {
        $this->nextNode = $errorNode;
    }

    public function getNextNode()
    {
        if ($this->nextNode) {
            return $this->nextNode;
        } else {
            return $this;
        }
    }

    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function setUserResponse($userResponse)
    {
        $this->userResponse = $userResponse;
    }

    public function getUserResponse()
    {
        return $this->userResponse;
    }

    protected function setErrorType($errorType)
    {
        $this->errorType = $errorType;
    }

    public function getErrorType()
    {
        return $this->errorType;
    }
}
