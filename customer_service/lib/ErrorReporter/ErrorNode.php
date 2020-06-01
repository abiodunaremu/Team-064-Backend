<?php
namespace Lib\ErrorReporter;

interface ErrorNode
{
    public function setPreviousNode($errorNode);
    public function getPreviousNode();
    public function setNextNode($errorNode);
    public function getNextNode();
    public function setErrorMessage($errorNode);
    public function getErrorMessage();
    public function setUserResponse($userResponse);
    public function getUserResponse();
    public function getErrorType();
}
