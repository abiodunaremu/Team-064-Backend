<?php
namespace Customer\Controllers;

use Customer\Controllers\ApplicationController;
use Lib\ResponseBuilder\JsonResponseBuilder;
use Lib\ResponseBuilder\JsonClientResponseData;
use Customer\Services\CustomerSessionService;
use Customer\Services\CustomerService;

class CustomerSessionController extends ApplicationController
{
    private $params;
    private $decoded;
    private $index_entity;
    private $customerSessionService;

    public function __construct($params, $decoded, $index_entity)
    {
        $this->params = $params;
        $this->decoded = $decoded;
        $this->index_entity = $index_entity;
        $this->customerSessionService = new CustomerSessionService();
    }
    
    public function processRequest()
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0) {
            //login customer session
            $username = $this->decoded['username'];
            $password = $this->decoded['password'];
            $this->loginCustomer($username, $password);
        } elseif (strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT') === 0) {
            //Logout customer session
            $jwt = $this->decoded['jwt'];
            $this->logoutCustomer($jwt);
        } elseif (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') === 0) {
            //verify customer session
            $jwt = $this->decoded['jwt'];
            $this->verifyCustomerSession($jwt);
        } else {
            $this->showInvalidRequest("customersessions");
        }
    }

    public function loginCustomer($username, $password)
    {
        //TODO:VALIDATE INPUTS
        $deviceType = "w";
        $region = "*";
        $startState = "0";

        $responseData = new JsonClientResponseData();
        $responseBuilder = new JsonResponseBuilder();
        $customerSession = $this->customerSessionService->loginCustomer(
            $username,
            $password,
            $startState,
            $deviceType,
            $region
        );

        if ($customerSession === null || $customerSession->getCustomerId() === null) {
            $responseData->addValue("href", "app/customersessions");
            $responseData->addValue("password", $password);
            $responseData->addValue("username", $username);
            echo $responseBuilder->setName("logincustomer")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(404);
            return;
        }

        $customerService = new CustomerService();
        $customer = $customerService->getCustomerById($customerSession->getCustomerId());
        
        if ($customer === null || $customer->getCustomerId() === null) {
            $responseData->addValue("href", "app/customersessions");
            // $responseData->addValue("password", $password);
            $responseData->addValue("username", $username);
            echo $responseBuilder->setName("logincustomer")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(404);
            return;
        } else {
            $responseData->addValue("href", "app/customersessions");
            $responseData->addValue("firstname", $customer->getFirstName());
            $responseData->addValue("lastname", $customer->getLastName());
            $responseData->addValue("email", $customer->getEmail());
            $responseData->addValue("country", $customer->getNationality());
            $responseData->addValue("dateofbirth", $customer->getDateOfBirth());
            $responseData->addValue("gender", $customer->getGender());
            $responseData->addValue("phonenumber", $customer->getPhoneNumber());
            $responseData->addValue("customerid", $customer->getCustomerId());
            $responseData->addValue("jwt", $this->customerSessionService->generateJwt($customerSession));
            
            echo $responseBuilder->setName("logincustomer")
            ->setStatus("successful")
            ->addClientResponse($responseData)
            ->build();
            http_response_code(201);
        }
    }

    public function logoutCustomer($jwt)
    {
        //TODO:VALIDATE INPUTS
        $responseData = new JsonClientResponseData();
        $responseBuilder = new JsonResponseBuilder();
        $logoutStatus = $this->customerSessionService->logoutCustomer($jwt);

        if ($logoutStatus === null || $logoutStatus === "") {
            $responseData->addValue("href", "app/customersessions");
            // $responseData->addValue("sid", $sessionId);
            echo $responseBuilder->setName("logoutcustomer")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(404);
            return;
        } else {
            $responseData->addValue("href", "app/customersessions");
            echo $responseBuilder->setName("logoutcustomer")
            ->setStatus("successful")
            ->addClientResponse($responseData)
            ->build();
            http_response_code(201);
        }
    }

    public function verifyCustomerSession($jwt)
    {
        //TODO:VALIDATE INPUTS
        $responseData = new JsonClientResponseData();
        $responseBuilder = new JsonResponseBuilder();
        $customerId = $this->customerSessionService->verifyCustomerSession($jwt);

        if ($customerId === null || $customerId === "") {
            $responseData->addValue("href", "app/customersessions");
            $responseData->addValue("jwt", $jwt);
            echo $responseBuilder->setName("verifycustomersession")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(401);
            return;
        } else {
            $responseData->addValue("href", "app/customersessions");
            $responseData->addValue("çustomerid", $customerId);
            echo $responseBuilder->setName("verifycustomersession")
            ->setStatus("successful")
            ->addClientResponse($responseData)
            ->build();
            http_response_code(200);
        }
    }
}
