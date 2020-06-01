<?php
namespace Customer\Services;

use Customer\Repositories\CustomerSessionRepository;
use Exception;
use Firebase\JWT\JWT;
use Lib\ErrorReporter\ErrorNodeFactory;
use Lib\ErrorReporter\ErrorReporter;

class CustomerSessionService
{
    private $customerSessionRepository;
    private $jwtKey;

    public function __construct()
    {
        $this->customerSessionRepository = new CustomerSessionRepository();
        $this->jwtKey = "team-064-jwt-key";
    }

    public function loginCustomer(
        $username,
        $password,
        $startState,
        $deviceType,
        $region
    )
    {
        //preprocessing of input
        return $this->customerSessionRepository->loginCustomer(
            $username,
            $password,
            $startState,
            $deviceType,
            $region
        );
    }

    public function logoutCustomer($jwt)
    {
        $jwtCustomerSession = $this->decryptJwt($jwt);
        $logoutStatus = "";
        if ($jwtCustomerSession) {
            $logoutStatus = $this->customerSessionRepository
            ->logoutCustomer($jwtCustomerSession->data->sessionid);
        }

        return $logoutStatus;
    }

    public function verifyCustomerSession($jwt)
    {
        $jwtCustomerSession = $this->decryptJwt($jwt);
        $sessionId = "";
        if ($jwtCustomerSession) {
            $sessionId = $jwtCustomerSession->data->sessionid;
        }
        return $this->customerSessionRepository->verifyCustomerSession($sessionId);
        ;
    }

    public function generateJwt($customerSession)
    {
        $issuer_claim = "CUSTOMER_SESSION"; // this can be the servername
        $audience_claim = "BROOMY_APP";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $jwt = "";

        if ($customerSession) {
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "data" => array(
                    "sessionid" => $customerSession->getCustomerSessionId(),
                    "customerid" => $customerSession->getCustomerId()
                    )
             );
    
            $jwt = JWT::encode($token, $this->jwtKey);
        }

        return $jwt;
    }

    public function decryptJwt($jwt)
    {
        $jwt_decoded = "";
        $errorNodeFactory = new ErrorNodeFactory();

        if ($jwt) {
            try {
                $jwt_decoded = JWT::decode($jwt, $this->jwtKey, array('HS256'));
            } catch (Exception $e) {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "CustomerSessionService->decryptJwtToken; Invalid jwt".$e,
                    "Invalid customer session"
                );
                ErrorReporter::addNode($errorNode);
            }
        }

        return $jwt_decoded;
    }
}
