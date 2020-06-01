<?php
namespace Customer\Repositories;

use Customer\Models\CustomerSessionModel;
use Lib\DatabaseManager\DatabaseManager;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;

/** Application level representation for executing the
 * customer session model related functions in the
 * Domain Driven Design*/

class CustomerSessionRepository
{
    private $customerSession;

    public function loginCustomer($username, $password, $startState, $deviceType, $region)
    {
        return $this->login($username, $password, $startState, $deviceType, $region);
    }

    private function login(
        $username,
        $password,
        $startState,
        $deviceType,
        $region
    )
    {
        $queryHolder = "SET @pLink='',@uID='',@sID=''";
        $querySelectHolder = "SELECT @uID AS 'u_id',@sID AS 's_id',
        @tIn AS 't_in',@tOut AS 't_out',@tLcheck AS 't_lcheck',
        @tOut AS 'c_state',@eState AS 'e_state'";
        $query = "CALL prnSessionLogin('".$username."','".$password."',
        '".$startState."','".$deviceType."','".$region."',@uID,@sID,@tIn,
        @tOut,@tLcheck,@cState,@eState);";
        
        $errorNodeFactory = new ErrorNodeFactory();
        if (DatabaseManager::mysql_query($queryHolder)) {
            if (DatabaseManager::mysql_query($query)) {
                $result = DatabaseManager::mysql_query($querySelectHolder);
                if ($result) {
                    $num_results = DatabaseManager::mysql_num_rows($result);
                    if ($num_results>0) {
                        $row = DatabaseManager::mysql_fetch_array($result);
                        if (strcmp($row["s_id"], "") != 0) {
                            $sessionId = $row["s_id"];
                            $customerId = $row["u_id"];
                            $timeIn = $row["t_in"];
                            $timeOut = $row["t_out"];
                            $timeLastChecked = $row["t_lcheck"];
                            $checkState = $row["c_state"];
                            $endState = $row["e_state"];
                            
                            $this->customerSession = new CustomerSessionModel(
                                $sessionId,
                                $customerId,
                                $timeIn,
                                $timeOut,
                                $timeLastChecked,
                                $startState,
                                $checkState,
                                $endState,
                                $deviceType,
                                $region
                            );
                        } else {
                            $errorNode = $errorNodeFactory->createPersistenceError(
                                "Class:CustomerSessionRepository->login; Session Id is empty ",
                                "Invalid username and/or password."
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:CustomerSessionRepository->login; Resultset is empty ",
                            "Invalid username and/or password."
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "Class:CustomerSessionRepository->login; Resultset is null ".
                        DatabaseManager::mysql_error(),
                        "Unable to login. Please try again later."
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:CustomerSessionRepository->login; Query failed".
                    DatabaseManager::mysql_error(),
                    "Internal error occured. Please try again later."
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:CustomerSessionRepository->login; Holder failed".
                DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->customerSession;
    }

    public function logoutCustomer($sessionId)
    {
        return $this->logout($sessionId);
    }
    
    private function logout($sessionId)
    {
        $queryHolder="SET @customerID=''";
        $querySelectHolder="SELECT @customerID AS 'c_id'";
        $query = "CALL prnUpdCustomerSession('".$sessionId."','0',@customerID);";
        $uId = "";
        $errorNodeFactory = new ErrorNodeFactory();
        if (DatabaseManager::mysql_query($queryHolder)) {
            if (DatabaseManager::mysql_query($query)) {
                $result = DatabaseManager::mysql_query($querySelectHolder);
                if ($result) {
                    $num_results = DatabaseManager::mysql_num_rows($result);
                    if ($num_results>0) {
                        $row = DatabaseManager::mysql_fetch_array($result);
                        if (strcmp($row["c_id"], "")!=0) {
                            $uId = $row["c_id"];
                        } else {
                            $errorNode = $errorNodeFactory->createPersistenceError(
                                "CustomerSessionRepository->logout; CustomerSession Id is empty sid=".$sessionId,
                                "Invalid customer session"
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "CustomerSessionRepository->logout; Resultset is empty ",
                            "Invalid customer session"
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "CustomerSessionRepository->logout; Resultset is null ".
                        DatabaseManager::mysql_error(),
                        "Unable to logout. Please try again later."
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "CustomerSessionRepository->logout; Query failed".
                    DatabaseManager::mysql_error(),
                    "Internal error occured. Please try again later."
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "CustomerSessionRepository->logout; Holder failed".
                DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $uId;
    }

    public function verifyCustomerSession($sessionId)
    {
        return $this->verifyActiveSession($sessionId);
    }

    private static function verifyActiveSession($sessionId)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $customerID="";
        $row="";
        $queryHolder="SET @cID=''";
        $querySelectHolder="SELECT @cID AS 'c_id'";
        $query = "CALL prnVerifyActiveCustomerSession('".$sessionId."',@cID);";
        
        if (DatabaseManager::mysql_query($queryHolder)) {
            if (DatabaseManager::mysql_query($query)) {
                $result = DatabaseManager::mysql_query($querySelectHolder);
                if ($result) {
                    $num_results = DatabaseManager::mysql_num_rows($result);
                    if ($num_results>0) {
                        $row = DatabaseManager::mysql_fetch_array($result);
                        if (strcmp($row['c_id'], "")!=0) {
                            $customerID = $row['c_id'];
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:ActiverUser->verifyActiveSession; Empty resultset ",
                            "Session expired. Please login again."
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "Class:ActiverUser->verifyActiveSession; Null resultset ".
                        DatabaseManager::mysql_error(),
                        "Internal error occured. Please login again."
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:ActiverUser->verifyActiveSession; Query failed ".
                    DatabaseManager::mysql_error(),
                    "Internal error occured. Please login again."
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:ActiverUser->verifyActiveSession; Holder failed ".
                DatabaseManager::mysql_error(),
                "Internal error occured. Please login again."
            );
            ErrorReporter::addNode($errorNode);
        }
        
        return $customerID;
    }
}
