<?php
namespace Lib\ResponseBuilder;

interface ResponseBuilder
{
    public function setName($name);
    public function getName();
    public function addClientResponse($clientResponse);
    public function setStatus($status);
}
