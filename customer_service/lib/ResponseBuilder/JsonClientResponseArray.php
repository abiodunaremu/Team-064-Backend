<?php
namespace Lib\ResponseBuilder;

use Lib\ResponseBuilder\JsonHandler;
use Lib\ResponseBuilder\ClientResponse;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;

class JsonClientResponseArray
{
    private $clientResponseName;
    private $response;
    private $count = 0;

    public function setName($name)
    {
        $this->clientResponseName = $name;
        return $this;
    }

    public function getName()
    {
        return $this->clientResponseName;
    }

    public function getResponseCount()
    {
        return $this->count;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function addResponse($data)
    {
        $jsonHandler = new JsonHandler();
        $errorNodeFactory = new ErrorNodeFactory();

        if ($data === null) {
            $errorNode = $errorNodeFactory->createObjectError(
                "JsonClientResponseArray->addResponse; data ".
                "is null dta: ".$data,
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
            return;
        }

        if ($this->count > 0) {
            $this->response = $this->response.",{".$data."}";
        } else {
            $this->response = $this->response."{".$data."}";
        }

        $this->count++;
        return $this;
    }

    public function __toString()
    {
        return "\"".$this->clientResponseName."\": [".$this->response."]";
    }
}

// $response = new JsonClientResponse();
// $response->setName("Object Infor");
// $response->addResponse("ID","0001");
// $response->addResponse("firstname","Abiodun");
// $response->addResponse("lastname","Aremu");
// $response->addResponse("Gender","Male");
// echo $response;
