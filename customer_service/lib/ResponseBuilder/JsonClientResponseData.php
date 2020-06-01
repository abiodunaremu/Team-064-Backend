<?php
namespace Lib\ResponseBuilder;

use Lib\ResponseBuilder\JsonHandler;
use Lib\ResponseBuilder\ClientResponse;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;

class JsonClientResponseData implements ClientResponseData
{
    private $data;
    private $count = 0;

    public function getValueCount()
    {
        return $this->count;
    }

    public function getData()
    {
        return $this->data;
    }

    public function addValue($key, $value)
    {
        $jsonHandler = new JsonHandler();
        $errorNodeFactory = new ErrorNodeFactory();

        if ($key === null || $value === null) {
            $errorNode = $errorNodeFactory->createObjectError(
                "JsonClientResponseData->addValue; key or ".
                "value is null key: ".$key.", value: ".$value,
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
            return;
        }

        if ($this->count > 0) {
            $this->data = $this->data.",\"".$jsonHandler->adjustJsonString($key).
            "\" : \"".$jsonHandler->adjustJsonString($value)."\"";
        } else {
            $this->data = $this->data."\"".$jsonHandler->adjustJsonString($key).
            "\" : \"".$jsonHandler->adjustJsonString($value)."\"";
        }

        $this->count++;
        return $this;
    }

    public function __toString()
    {
        return $this->data;
    }
}

// data = new JsonClientResponse();
// data->setName("Object Infor");
// data->addResponse("ID","0001");
// data->addResponse("firstname","Abiodun");
// data->addResponse("lastname","Aremu");
// data->addResponse("Gender","Male");
// echo data;
