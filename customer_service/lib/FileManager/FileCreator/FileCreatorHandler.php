<?php
namespace Lib\FileManager\FileCreator;

use Lib\Database\DatabaseManager;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;

class FileCreatorHandler
{
    private $fileCreator;
    private $fileCreators;
    private $status;

    public function __construct()
    {
    }

    public function persistFileCreator($name, $description, $fileDestinationPath)
    {
        $error = "";
        $response = "";
        $errorNodeFactory = new ErrorNodeFactory();
        $queryHolder="SET @sID=''";
        $querySelectHolder="SELECT @sID AS 's_id'";
        $query = "CALL prnInsFileCreator('".$name."', '"
        .$description."', '".$fileDestinationPath."', @sID);";
        if (DatabaseManager::mysql_query($queryHolder)) {
            if (DatabaseManager::mysql_query($query)) {
                $result = DatabaseManager::mysql_query($querySelectHolder);
                if ($result) {
                    $num_results = DatabaseManager::mysql_num_rows($result);
                    if ($num_results>0) {
                        $row = DatabaseManager::mysql_fetch_array($result);
                        if (strcmp($row["s_id"], "")!=0) {
                            $this->status = $row["s_id"];
                        } else {
                            $errorNode = $errorNodeFactory->createPersistenceError(
                                "Class:FileCreatorHandler->persistFileCreator; Empty status returned ",
                                "Unable to register file type.<br/>Please try again Later"
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:FileCreatorHandler->persistFileCreator; Empty status returned ",
                            "Unable to register file type.<br/>Please try again Later"
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "Class:FileCreatorHandler->persistFileCreator; Null resultset: ".
                        DatabaseManager::mysql_error(),
                        "Unable to register file type.<br/>Please try again Later"
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:FileCreatorHandler->persistFileCreator; Query failed: ".
                    DatabaseManager::mysql_error(),
                    "Unable to register file type.<br/>Please try again Later"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:FileCreatorHandler->persistFileCreator; Holder failed: ".
                DatabaseManager::mysql_error(),
                "Unable to register file type.<br/>Please try again Later"
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->status;
    }

    public function getFileCreators()
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM FileCreator WHERE cStatus='0';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);

        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $this->fileCreators = [];
                for ($x = 0; $x < $num_results; $x++) {
                    $row = DatabaseManager::mysql_fetch_array($result);
                    array_push($this->fileCreators, new FileCreator(
                        $row["iFileCreatorId"],
                        $row["vName"],
                        $row["tDescription"],
                        $row["tFileDestinationPath"],
                        $row["dCreateDate"],
                        $row["dLastUpdate"],
                        $row["cStatus"]
                    ));
                }
            } else {
                // $response = "Error! Session expired";
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "FileCreatorHandler->getFileCreators; Empty resultset",
                    "Job type does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "FileCreatorHandler->getFileCreators; Null resultst: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }

        return $this->fileCreators;
    }

    public function getFileCreatorById($fileCreatorId)
    {
        return $this->getFileCreator($fileCreatorId);
    }
    
    protected function getFileCreator($fileCreatorId)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM FileCreator where iFileCreatorId='".$fileCreatorId."';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);
        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $row = DatabaseManager::mysql_fetch_array($result);
                $this->fileCreator = new FileCreator(
                    $row["iFileCreatorId"],
                    $row["vName"],
                    $row["tDescription"],
                    $row["tFileDestinationPath"],
                    $row["dCreateDate"],
                    $row["dLastUpdate"],
                    $row["cStatus"]
                );
            } else {
                // $response = "Error! Session expired";
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "FileCreatorHandler->getFileCreator; Empty resultset for id '".$fileCreatorId."'",
                    "The file type requested does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "FileCreatorHandler->getFileCreator; Null resultst: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->fileCreator;
    }

    public function getFileCreatorByName($fileCreatorName)
    {
        return $this->getFileCreatorUsingName($fileCreatorName);
    }
    
    protected function getFileCreatorUsingName($fileCreatorName)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM FileCreator where vName='".$fileCreatorName."';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);
        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $row = DatabaseManager::mysql_fetch_array($result);
                $this->fileCreator = new FileCreator(
                    $row["iFileCreatorID"],
                    $row["vName"],
                    $row["tDescription"],
                    $row["tFileDestinationPath"],
                    $row["dCreateDate"],
                    $row["dLastUpdate"],
                    $row["cStatus"]
                );
            } else {
                // $response = "Error! Session expired";
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "FileCreatorHandler->getFileCreatorByName; Empty resultset for name '".$fileCreatorName."'",
                    "The file type requested does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "FileCreatorHandler->getFileCreatorByName; Null resultst: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->fileCreator;
    }


    public function deleteFileCreator($fileTypeCode)
    {
        $error = "";
        $errorNodeFactory = new ErrorNodeFactory();
        $queryHolder="SET @sID=''";
        $querySelectHolder="SELECT @sID AS 's_id'";
        $query = "CALL prnUpdFileCreator('".$fileTypeCode.
        "','','1',@sID);";
        if (DatabaseManager::mysql_query($queryHolder)) {
            if (DatabaseManager::mysql_query($query)) {
                $result = DatabaseManager::mysql_query($querySelectHolder);
                if ($result) {
                    $num_results = DatabaseManager::mysql_num_rows($result);
                    if ($num_results>0) {
                        $row = DatabaseManager::mysql_fetch_array($result);
                        if (strcmp($row["s_id"], "")!=0) {
                            $this->status = $row["s_id"];
                        } else {
                            $errorNode = $errorNodeFactory->createPersistenceError(
                                "Class:FileCreatorHandler->deleteFileCreator; Empty session id returned ",
                                "Unable to delete file type.<br/>Please try again Later"
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:FileCreatorHandler->deleteFileCreator; Empty result set returned",
                            "Unable to delete file type.<br/>Please try again Later"
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "FileCreatorHandler->deleteFileCreator; Null resultset: ".
                        DatabaseManager::mysql_error(),
                        "Unable to delete file type.<br/>Please try again Later"
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:FileCreatorHandler->deleteFileCreator; Query failed: ".
                    DatabaseManager::mysql_error(),
                    "Unable to delete file type.<br/>Please try again Later"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:FileCreatorHandler->deleteFileCreator; Holder failed: ".
                DatabaseManager::mysql_error(),
                "Unable to delete file type.<br/>Please try again Later"
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->status;
    }
}
