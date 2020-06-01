<?php
namespace Customer\Controllers;

use Lib\ResponseBuilder\JsonResponseBuilder;
use Lib\ResponseBuilder\JsonClientResponseData;

class ApplicationController
{
    public function __construct()
    {
    }

    public function showInvalidRequest($source)
    {
        $responseData = new JsonClientResponseData();
        $responseBuilder = new JsonResponseBuilder();

        $responseData->addValue("href", "app/documentation");
        $responseData->addValue("source", $source);

        echo $responseBuilder->setName("invalidrequest")
        ->setStatus(0)
        ->addClientResponse($responseData)
        ->build();
    }
}
