<?php
namespace Customer\Controllers;

use Lib\ResponseBuilder\JsonResponseBuilder;
use Lib\ResponseBuilder\JsonClientResponseData;
use Lib\ResponseBuilder\JsonClientResponseArray;
use Customer\Services\CustomerService;
use Customer\Controllers\ApplicationController;
use GuzzleHttp\Client;

class CustomerController extends ApplicationController
{
    private $params;
    private $decoded;
    private $index_entity;
    private $customerService;
    private $customer;

    public function __construct($params, $decoded, $index_entity)
    {
        $this->params = $params;
        $this->decoded = $decoded;
        $this->index_entity = $index_entity;
        $this->customerService = new CustomerService();
    }
    
    public function processRequest()
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0) {
            //Signup a customer
            $this->signupCustomer(
                $this->decoded['firstname'],
                $this->decoded['lastname'],
                $this->decoded['phonenumber'],
                $this->decoded['email'],
                $this->decoded['gender'],
                $this->decoded['country'],
                $this->decoded['dateofbirth']
            );
        } elseif (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET')
            === 0 && sizeof($this->params) === $this->index_entity+2) {
            //retrieve customer information by id
            $customerId = $this->params[sizeof($this->params)-1];
            $jwt = $this->decoded["jwt"];
            $this->getCustomerDetails($customerId, $jwt);
        } elseif (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET')
        === 0
        && sizeof($this->params) === $this->index_entity+4
        && strcasecmp($this->params[sizeof($this->params)-2], 'search') === 0) {
            //search customer information by criteria
            $criteria = $this->params[sizeof($this->params)-1];
            $jwt = $this->decoded["jwt"];
            $this->searchCustomers($criteria, $jwt);
        } elseif (strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT') === 0
        && sizeof($this->params) === $this->index_entity+3
        && strcasecmp($this->params[sizeof($this->params)-1], 'profilepicture') === 0) {
            //upload profile picture
            $jwt = $this->decoded['jwt'];
            $filePath = $_FILES['customerprofilepicture']["tmp_name"];
            $fileName = $_FILES['customerprofilepicture']["name"];
            $fileSize = $file_size=$_FILES["customerprofilepicture"]["size"];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $this->uploadCustomerProfilePicture(
                $jwt,
                $filePath,
                $fileName,
                $fileSize,
                $fileExtension
            );
        } elseif (strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT') === 0) {
            //reset customer password
            $email = $this->decoded['email'];
            $phoneNumber = $this->decoded['phonenumber'];
            $this->resetCustomerPassword($email, $phoneNumber);
        } else {
            $this->showInvalidRequest("customers");
        }
    }

    private function signupCustomer($firstName, $lastName, $phoneNumber, $email, $gender, $country, $dateOfBirth)
    {
        //TODO:VALIDATE INPUTS
        $deviceType = "w";
        $region = "*";
        
        $this->customer = $this->customerService
        ->registerCustomer(
            $firstName,
            $lastName,
            $phoneNumber,
            $email,
            $dateOfBirth,
            $gender,
            $country,
            $deviceType,
            $region
        );
        
        $responseData = new JsonClientResponseData();
        $responseBuilder = new JsonResponseBuilder();

        if ($this->customer === null) {
            $responseData->addValue("href", "app/customers");
            $responseData->addValue("dateofbirth", $dateOfBirth);
            echo $responseBuilder->setName("signupcustomer")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(404);
            return;
        } else {
            $responseData->addValue("firstname", $this->customer->getFirstName());
            $responseData->addValue("lastname", $this->customer->getLastName());
            $responseData->addValue("email", $this->customer->getEmail());
            $responseData->addValue("password", $this->customer->getPassword());
            
            echo $responseBuilder->setName("signupcustomer")
            ->setStatus("successful")
            ->addClientResponse($responseData)
            ->build();
            http_response_code(201);
        }
    }

    private function getCustomerDetails($customerId, $jwt)
    {
        //TODO:VALIDATE INPUTS

        //Authorize user
        $headers = array(
            "Content-type" => "application/json",
            "Token" => $jwt
        );
        $request_method = "GET";
        $url = "localhost:81/broomy_demo/customersessions";
        $http_client = new Client();
        $response = $http_client->request($request_method, $url, ['headers' => $headers]);
        $decodedResponse = json_decode($response->getBody(), true);
        
        $responseData = new JsonClientResponseData();
        $responseBuilder = new JsonResponseBuilder();

        if (!is_array($decodedResponse) || $decodedResponse['status'] === 'failed') {
            $responseData->addValue("href", "app/customers");
            $responseData->addValue("authentication", "failed");
            echo $responseBuilder->setName("getcustomerdetails")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(404);
            return;
        }

        $this->customer = $this->customerService->getCustomerById($customerId);

        if ($this->customer === null) {
            $responseData->addValue("href", "app/customers");
            echo $responseBuilder->setName("getcustomerdetails")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(404);
            return;
        }

        $responseData->addValue("firstname", $this->customer->getFirstName());
        $responseData->addValue("middlename", $this->customer->getMiddleName());
        $responseData->addValue("lastname", $this->customer->getLastName());
        $responseData->addValue("Gender", $this->customer->getGender());
        $responseData->addValue("County", $this->customer->getNationality());
        $responseData->addValue("dateofbirth", $this->customer->getDateOfBirth());
        $responseData->addValue("phonenumber", $this->customer->getPhoneNumber());
        $responseData->addValue("email", $this->customer->getEmail());
        $responseData->addValue("profilepicture", $this->customer->getImage());
        $responseData->addValue("datesignup", $this->customer->getDateSignup());
        $responseData->addValue("accountstatus", $this->customer->getStatus());
        echo $responseBuilder->setName("getcustomerdetails")
        ->setStatus("successful")
        ->addClientResponse($responseData)
        ->build();

        http_response_code(200);
    }

    private function searchCustomers($criteria, $jwt)
    {
        //TODO:VALIDATE INPUTS
        $customers = $this->customerService->searchCustomersByCriteria($criteria);
        $responseBuilder = new JsonResponseBuilder();
        $responseArray = new JsonClientResponseArray();
        $responseData = new JsonClientResponseData();
        $responseArray->setName("customers");

        //Authenticate API user
        $headers = array(
            "Content-type" => "application/json",
            "Token" => $jwt
        );
        $request_method = "GET";
        $url = "localhost:81/broomy_demo/customersessions";
        $http_client = new Client();
        $response = $http_client->request($request_method, $url, ['headers' => $headers]);
        $decodedResponse = json_decode($response->getBody(), true);

        if (!is_array($decodedResponse) || $decodedResponse['status'] === 'failed') {
            $responseData->addValue("authentication", "failed");
            $responseData->addValue("href", "app/customers/search");
            echo $responseBuilder->setName("searchcustomers")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(500);
            return;
        }

        if ($customers === null) {
            $responseData->addValue("href", "app/customers/search");
            echo $responseBuilder->setName("searchcustomers")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(500);
            return;
        }

        foreach ($customers as $customer) {
            $responseData = new JsonClientResponseData();

            $responseData->addValue("customerid", $customer->getCustomerId());
            $responseData->addValue("firstname", $customer->getFirstName());
            // $responseData->addValue("middlename",$customer->getMiddleName());
            $responseData->addValue("lastname", $customer->getLastName());
            $responseData->addValue("gender", $customer->getGender());
            $responseData->addValue("country", $customer->getNationality());
            $responseData->addValue("dateofbirth", $customer->getDateOfBirth());
            $responseData->addValue("phonenumber", $customer->getPhoneNumber());
            $responseData->addValue("email", $customer->getEmail());
            // $responseData->addValue("alias",$customer->getAlias());
            $responseData->addValue("profilepicture", $customer->getImage());
            $responseData->addValue("datesignup", $customer->getDateSignup());
            $responseData->addValue("accountstatus", $customer->getStatus());
            $responseArray->addResponse($responseData);
        }

        echo $responseBuilder->setName("searchcustomers")
        ->setStatus("successful")
        ->addClientResponse($responseArray)
        ->build();

        http_response_code(200);
    }
    
    private function resetCustomerPassword($email, $phoneNumber)
    {
        //TODO:VALIDATE INPUTS
        $this->customer = $this->customerService
        ->resetCustomerPassword($email, $phoneNumber);
        
        $responseData = new JsonClientResponseData();
        $responseBuilder = new JsonResponseBuilder();

        if ($this->customer === null) {
            $responseData->addValue("href", "app/customers");
            echo $responseBuilder->setName("resetcustomerpassword")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(404);
            return;
        } else {
            $responseData->addValue("firstname", $this->customer->getFirstName());
            $responseData->addValue("lastname", $this->customer->getLastName());
            $responseData->addValue("email", $this->customer->getEmail());
            $responseData->addValue("password", $this->customer->getPassword());

            echo $responseBuilder->setName("resetcustomerpassword")
            ->setStatus("successful")
            ->addClientResponse($responseData)
            ->build();
            http_response_code(201);
        }
    }

    public function uploadCustomerProfilePicture($sessionId, $filePath, $fileName, $fileSize, $fileExtension)
    {
        //TODO:VALIDATE INPUTS
        $responseData = new JsonClientResponseData();
        $responseBuilder = new JsonResponseBuilder();
        $fileGroupId = $this->customerService->uploadProfilePicture($sessionId, $fileName, $fileExtension, $filePath, $fileSize);

        if ($fileGroupId === null) {
            $responseData->addValue("href", "app/customers/@id/profilepicture");
            $responseData->addValue("sessionid", $sessionId);
            echo $responseBuilder->setName("uploadcustomerprofilepicture")
            ->setStatus("failed")
            ->addClientResponse($responseData)
            ->build();
            // http_response_code(404);
            return;
        } else {
            $responseData->addValue("href", "app/customers/@id/profilepicture");
            $responseData->addValue("sessionid", $sessionId);
            $responseData->addValue("filegroupid", $fileGroupId);
                
            echo $responseBuilder->setName("uploadcustomerprofilepicture")
            ->setStatus("successful")
            ->addClientResponse($responseData)
            ->build();
            http_response_code(201);
        }
    }
}
