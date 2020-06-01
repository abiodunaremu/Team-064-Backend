<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("access-control-allow-origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("access-control-allow-methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Or_+_igin, X-Requested-With, Content-Type, Accept, Authorization,Access-Control-Allow-Origin,access-control-allow-headers");
header("access-control-allow-headers: Origin, X-Requested-With, Content-Type, Accept, Authorization,Access-Control-Allow-Origin,access-control-allow-headers,access-control-allow-methods");

error_reporting(E_ALL);
ini_set('display_errors',1);

require_once "vendor/autoload.php";

use Lib\ErrorReporter\ErrorReporter;
use Customer\Controllers\ApplicationController;
use Customer\Controllers\CustomerController;
use Customer\Controllers\CustomerSessionController;

//instantiating and configuring ErrorReporter
ErrorReporter::getInstance();
ErrorReporter::setErrorTraceMode(1);
ErrorReporter::setResponseMessageType(0);

$applicationController = new ApplicationController();
$decoded = [];

//Process POST | PUT | DELETE request.
if(in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT', 'DELETE'))) {
    
    //Make sure that the content type of the request has been set to application/json
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if(strcasecmp($contentType, 'application/json') != 0){
        $applicationController->showInvalidRequest("preprocessing_content");
        return;
    }

    //Receive the RAW post data.
    $content = trim(file_get_contents("php://input"));

    //Attempt to decode the incoming RAW post data from JSON.
    $decoded = json_decode($content, true);

    //If json_decode failed, the JSON is invalid.
    if(!is_array($decoded)){
        $applicationController->showInvalidRequest("preprocessing_json");
        return;
    }
}

$path = $_SERVER['REQUEST_URI'];
$headers = apache_request_headers();

if(isset($headers['Token'])) {
    $decoded["jwt"] = $headers['Token'];
}

// $_SERVER['REQUEST_METHOD'] = isset($decoded['requesttype']) ? strtoupper($decoded['requesttype']) : "POST";
//  echo "...".$decoded['requesttype'].$_SERVER['REQUEST_METHOD']."-->Request Method";
$params     = explode("/", $path);
$safe_pages = array("customers", "customersessions");

$index_entity_local = 2;
$index_entity_live = 2;
$index_entity = $index_entity_local;

if(isset($params[$index_entity]) && is_array($decoded) && count($params) >= 1 && in_array($params[$index_entity], $safe_pages)) {
    if(strcmp($params[$index_entity], "customers") == 0) {
        $customerController = new CustomerController($params, $decoded, $index_entity);
        $customerController->processRequest();
    } else if(strcmp($params[$index_entity], "customersessions") == 0) {
        $customerSessionController = new CustomerSessionController($params, $decoded, $index_entity);
        $customerSessionController->processRequest();
    } else {
        $applicationController->showInvalidRequest("request_api");
    }
} else if(isset($params[$index_entity]) && $params[$index_entity] === "documentations") {
    // $_POST['source'] = "documentation";
    header("Content-Type: text/html");
    include "./doc/Documentation.php";
} else {
    $applicationController->showInvalidRequest("request_uri");
}
?>