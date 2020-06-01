<?php
namespace Customer\Repositories;

use Customer\Models\CustomerModel;
use Lib\DatabaseManager\DatabaseManager;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;

/** Application level representation for executing the
 * customer model related functions in the
 * Domain Driven Design*/
class CustomerRepository
{
    private $customer;
    private $customers;

    public function generatePassword($email)
    {
        $pass2=explode("@", $email);
        $checkLen=strlen($pass2[0]);
        $passInit=rand(1, $checkLen>=3?$checkLen-3:$checkLen);
        $pass1=substr($email, $passInit, $checkLen>=3?3:$checkLen);
        $password=$pass1."&Ab".rand(0, 99);
        return $password;
    }
    
    public function registerCustomer(
        $firstName,
        $lastName,
        $phone,
        $email,
        $dob,
        $gender,
        $country,
        $deviceType,
        $region
    ) {
        $errorNodeFactory = new ErrorNodeFactory();
        if ($this->customer != null) {
            $errorNode = $errorNodeFactory->createObjectError(
                "Customer Object already exist: will not create new user ",
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
            return null;
        }

        $this->customer = $this->createCustomer(
            $firstName,
            $lastName,
            $phone,
            $email,
            $dob,
            $gender,
            $country,
            $deviceType,
            $region
        );

        return $this->customer;
    }

    private function createCustomer(
        $firstName,
        $lastName,
        $phone,
        $email,
        $dob,
        $gender,
        $country,
        $deviceType,
        $region
    ) {
        $error = "";
        $response = "";
        $sessionId = "";
        $password = $this->generatePassword($email);
        $errorNodeFactory = new ErrorNodeFactory();
        $queryHolder="SET @sID=''";
        $querySelectHolder="SELECT @sID AS 's_id'";
        $query = "CALL prnSignUpCustomer('".$firstName."','".$lastName.
                "','".$phone."','".$email."','".$password."','".$dob.
                "','".$gender."','".$country."','".$deviceType."','".
                $region."',@sID);";
        $emailExist = $this->emailExist($email);
        $phoneExist = $this->phoneExist($phone);
        if ($emailExist === "exist") {
            // $response = "Email already exist.";
        
            $errorNode = $errorNodeFactory->createObjectError(
                "Class:CustomerRepository->createCustomer; emailExist returned exist = ".$emailExist,
                "A user with your email already exists.<br/>If you don't remember your password please click the forgot password link "
            );
            ErrorReporter::addNode($errorNode);
        } elseif ($phoneExist === "exist") {
            $errorNode = $errorNodeFactory->createObjectError(
                "Class:CustomerRepository->createCustomer; phoneExist returned 'exist' = ".$phoneExist,
                "<strong> A user with your phone number </strong> already exists.<br/>If you don't remember your password please click the forgot password link"
            );
            ErrorReporter::addNode($errorNode);
        } elseif (DatabaseManager::mysql_query($queryHolder)) {
            if (DatabaseManager::mysql_query($query)) {
                $result = DatabaseManager::mysql_query($querySelectHolder);
                if ($result) {
                    $num_results = DatabaseManager::mysql_num_rows($result);
                    if ($num_results > 0) {
                        $row = DatabaseManager::mysql_fetch_array($result);
                        if (strcmp($row["s_id"], "")!=0) {
                            $customerId = $row["s_id"];

                            $this->customer = new CustomerModel(
                                $customerId,
                                $firstName,
                                "",
                                $lastName,
                                $dob,
                                $gender,
                                $country,
                                $phone,
                                $email,
                                $password,
                                "",
                                "",
                                "0"
                            );
                        } else {
                            $errorNode = $errorNodeFactory->createPersistenceError(
                                "Class:CustomerRepository->createCustomer; Empty session id returned ".$query,
                                "Unable to complete sign up.<br/>Please try again Later"
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:CustomerRepository->createCustomer; Empty result set returned",
                            "Unable to complete sign up.<br/>Please try again Later"
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "Class:CustomerRepository->createCustomer; Null resultset: ".
                        DatabaseManager::mysql_error(),
                        "Unable to complete sign up.<br/>Please try again Later"
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:CustomerRepository->createCustomer; Query failed: ".$query.
                    DatabaseManager::mysql_error(),
                    "Unable to complete sign up.<br/>Please try again Later"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:CustomerRepository->createCustomer; Holder failed: ".
                DatabaseManager::mysql_error(),
                "Unable to complete sign up.<br/>Please try again Later"
            );
            ErrorReporter::addNode($errorNode);
        }

        return $this->customer;
    }

    public function emailExist($email)
    {
        $exist="not";
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT vEmail FROM Customer WHERE vEmail='".$email."';";
        $result = DatabaseManager::mysql_query($query);
        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $exist = "exist";
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:CustomerRepository->emailExist; Null resulset: ".
                DatabaseManager::mysql_error(),
                "Internal error occurred. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $exist;
    }

    public function phoneExist($phone)
    {
        $exist="not";
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT vPhone FROM Customer WHERE vPhone='".$phone."';";
        $result = DatabaseManager::mysql_query($query);
        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results > 0) {
                $exist="exist";
                return $exist;
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:CustomerRepository->phoneExist; Null resulset: ".
                DatabaseManager::mysql_error(),
                "Internal error occurred. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $exist;
    }

    public function getCustomerById($customerId)
    {
        return $this->getCustomer($customerId);
    }
    
    protected function getCustomer($customerId)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM vwCustomer where vCustomerID='".$customerId."';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);
        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $row = DatabaseManager::mysql_fetch_array($result);
                $this->customer = new CustomerModel(
                    $row["vCustomerID"],
                    $row["vFirstName"],
                    $row["vMiddleName"],
                    $row["vLastName"],
                    $row["dDOB"],
                    $row["cGender"],
                    $row["vCountryName"],
                    $row["vPhoneNumber"],
                    $row["vEmail"],
                    "",
                    $row["vMediaGroupID"],
                    $row["dDateCreated"],
                    ""
                );
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "CustomerHandler->getCustomer; Empty resultset for customerId '".$customerId."'",
                    "The customer requested does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "CustomerHandler->getCustomer; Null resultset: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->customer;
    }

    public function searchCustomersByCriteria($criteria)
    {
        return $this->searchCustomers($criteria);
    }

    protected function searchCustomers($criteria)
    {
        $errorNodeFactory = new ErrorNodeFactory();

        $query = "SELECT * FROM vwCustomer WHERE vCustomerID='".$criteria."' "
        ."OR vFirstName LIKE '%".$criteria."%' OR vLastName LIKE '%".$criteria."%'"
        ."OR vEmail LIKE '%".$criteria."%' OR vPhoneNumber LIKE '%".$criteria."%';";

        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);
        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $this->customers = [];
                for ($x = 0; $x < $num_results; $x++) {
                    $row = DatabaseManager::mysql_fetch_array($result);
                    array_push($this->customers, new CustomerModel(
                        $row["vCustomerID"],
                        $row["vFirstName"],
                        $row["vMiddleName"],
                        $row["vLastName"],
                        $row["dDOB"],
                        $row["cGender"],
                        $row["vCountryName"],
                        $row["vPhoneNumber"],
                        $row["vEmail"],
                        "",
                        $row["vMediaGroupID"],
                        $row["dDateCreated"],
                        ""
                    ));
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "CustomerHandler->searchCustomers; Empty resultset for criteria '".$criteria."'",
                    "The customer requested does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "CustomerHandler->getCustomer; Null resultset: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->customers;
    }

    private function generateResetPassword($email)
    {
        $pass2=explode("@", $email);
        $checkLen=strlen($pass2[0]);
        $passInit=rand(1, $checkLen>=3?$checkLen-3:$checkLen);
        $pass1=substr($email, $passInit, $checkLen>=3?3:$checkLen);
        $password=$pass1."%aC*".rand(0, 99);
        return $password;
    }

    public function resetCustomerPassword($email, $phoneNumber)
    {
        return $this->resetPassword(
            $email,
            $phoneNumber
        );
    }

    private function resetPassword($email, $phone)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $uID = "";
        $password = $this->generateResetPassword($email);
        $queryHolder = "SET @uID=''";
        $querySelectHolder = "SELECT @uID AS 'u_id'";
        $query = "CALL prnUpdCustomerPassword('".$email."','".$phone.
        "','".$password."',@uID);";
        
        if (DatabaseManager::mysql_query($queryHolder)) {
            if (DatabaseManager::mysql_query($query)) {
                $result = DatabaseManager::mysql_query($querySelectHolder);
                if ($result) {
                    $num_results = DatabaseManager::mysql_num_rows($result);
                    if ($num_results > 0) {
                        $row = DatabaseManager::mysql_fetch_array($result);
                        if (strcmp($row["u_id"], "") != 0) {
                            $uID = $row["u_id"];
                            $this->customer = $this->getCustomerById($uID);
                            $this->customer->setPassword($password);
                        } else {
                            $errorNode = $errorNodeFactory->createPersistenceError(
                                "CustomerHandler->resetPassword; No associated customer id returned",
                                "Invalid email and phone number combination."
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "CustomerHandler->resetPassword; Empyty result set from query",
                            "Invalid eamil and/or phone number."
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "CustomerHandler->resetPassword; Null result set: ".DatabaseManager::mysql_error(),
                        "Unable to login. Please try again later."
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "CustomerHandler->resetPassword; Query failed: ".DatabaseManager::mysql_error(),
                    "Unable to Login. Please try again later"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "CustomerHandler->resetPassword; Holder failed: ".DatabaseManager::mysql_error(),
                "Unable to login. Please try again later"
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->customer;
    }
}
