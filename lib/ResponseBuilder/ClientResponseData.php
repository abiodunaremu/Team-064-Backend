<?php
namespace Lib\ResponseBuilder;

interface ClientResponseData
{
    public function addValue($key, $response);
    public function getData();
    public function getValueCount();
}
