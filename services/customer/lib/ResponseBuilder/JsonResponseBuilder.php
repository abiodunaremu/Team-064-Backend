<?php
namespace Lib\ResponseBuilder;

use Lib\ResponseBuilder\JsonClientResponse;
use Lib\ResponseBuilder\ResponseBuilder;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;

class JsonResponseBuilder implements ResponseBuilder
{
    private $name;
    private $clientResponse;
    private $status;
    private $count;
        
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function addClientResponse($response)
    {
        $errorNodeFactory = new ErrorNodeFactory();

        if ($response === null) {
            $errorNode = $errorNodeFactory->createObjectError(
                "JsonResponseBuilder->addClientResponse; response ".
                " is null respose: ".$response,
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
            return;
        }

        if ($this->count > 0) {
            $this->clientResponse =
            $this->clientResponse.",".$response;
        } else {
            $this->clientResponse =
            $this->clientResponse.$response;
        }
        $this->count++;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function build()
    {
        $jsonHandler = new JsonHandler();
        if ($this->clientResponse === null) {
            $errorNodeFactory = new ErrorNodeFactory();
            $errorNode = $errorNodeFactory->createObjectError(
                "Class:JsonResponseBuilser->addResponse; client ".
                "response is null clientResponse: ".$this->clientResponse,
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
            return;
        }
        $errorNodeCount = ErrorReporter::getNodeCount();
        $errorCurrentNode = ErrorReporter::getHeadNode();
        $errorMessages = "";

        if (ErrorReporter::$errorTraceMode === 1) {
            $errorMessages = $errorMessages.$jsonHandler->quoteString("errors")
            ." : [{".
            $jsonHandler->quoteString("head")." : ";
            $headError = ErrorReporter::getResponseMessageType() === 0 ?
            $jsonHandler->quoteString($errorCurrentNode->getErrorMessage()) :
            $jsonHandler->quoteString($errorCurrentNode->getUserResponse());
            $errorMessages = $errorMessages.$headError."}";
            for ($index = 1; $index < $errorNodeCount; $index++) {
                $errorCurrentNode = $errorCurrentNode->getNextNode();
                if ($errorCurrentNode !== null) {
                    $errorMessages = $errorMessages.",{".
                    $jsonHandler->quoteString($index)." : ".(
                        ErrorReporter::getResponseMessageType() === 0 ?
                    $jsonHandler->quoteString($errorCurrentNode->getErrorMessage()) :
                    $jsonHandler->quoteString($errorCurrentNode->getUserResponse())
                    )."}";
                }
            }
            $errorMessages = $errorMessages."],";
        } else {
            $errorHeadNodeMessage = ErrorReporter::getResponseMessageType() === 0 ?
            $jsonHandler->quoteString($errorCurrentNode->getErrorMessage()) :
            $jsonHandler->quoteString($errorCurrentNode->getUserResponse());

            $errorMessages = $jsonHandler->quoteString("error")." : "
            .$errorHeadNodeMessage.",";
        }
        return
            // "{".$jsonHandler->quoteString($this->name)." : ".
                "{".$jsonHandler->quoteString("name")." : ".
                $jsonHandler->quoteString($this->name).",".
                $jsonHandler->quoteString("status")." : ".
                $jsonHandler->quoteString($this->status).",".
                $errorMessages.
                $jsonHandler->quoteString("errorcount")." : ".
                $jsonHandler->quoteString($errorNodeCount).",".
                $this->clientResponse."}";
    }
}
