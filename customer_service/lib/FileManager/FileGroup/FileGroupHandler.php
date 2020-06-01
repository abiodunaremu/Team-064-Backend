<?php
namespace Lib\FileManager\FileGroup;

use Lib\Database\DatabaseManager;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;

class FileGroupHandler
{
    private $fileGroup;
    private $fileGroups;
    private $status;

    public function __construct()
    {
    }

    public function persistFileGroup($sessionId, $creatorId)
    {
        $error = "";
        $response = "";
        $errorNodeFactory = new ErrorNodeFactory();
        $queryHolder="SET @sID=''";
        $querySelectHolder="SELECT @sID AS 's_id'";
        $query = "CALL prnInsFileGroup('".$sessionId."', '".$creatorId.
                "', @sID);";
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
                                "Class:FileGroupHandler->persistFileGroup; Empty status returned ",
                                "Unable to register file group.<br/>Please try again Later"
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:FileGroupHandler->persistFileGroup; Empty status returned ",
                            "Unable to register file group.<br/>Please try again Later"
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "Class:FileGroupHandler->persistFileGroup; Null resultset: ".
                        DatabaseManager::mysql_error(),
                        "Unable to register file group.<br/>Please try again Later"
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:FileGroupHandler->persistFileGroup; Query failed: ".
                    DatabaseManager::mysql_error(),
                    "Unable to register file group.<br/>Please try again Later"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:FileGroupHandler->persistFileGroup; Holder failed: ".
                DatabaseManager::mysql_error(),
                "Unable to register file group.<br/>Please try again Later"
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->status;
    }

    public function getFileGroups($userId)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM FileGroup WHERE vUserID='".$userId."' AND cStatus='0';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);

        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $this->fileGroups = [];
                for ($x = 0; $x < $num_results; $x++) {
                    $row = DatabaseManager::mysql_fetch_array($result);
                    array_push($this->fileGroups, new FileGroup(
                        $row["vFileGroupCode"],
                        $row["VUserID"],
                        $row["iFileCreatorID"],
                        $row["dCreateDate"],
                        $row["dLastUpdate"],
                        $row["cStatus"]
                    ));
                }
            } else {
                // $response = "Error! Session expired";
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "FileGroupHandler->getFileGroups; Empty resultset",
                    "File group does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "FileGroupHandler->getFileGroups; Null resultst: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }

        return $this->fileGroups;
    }

    public function getFileGroupById($fileGroupId)
    {
        return $this->getFileGroup($fileGroupId);
    }
    
    protected function getFileGroup($fileGroupId)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM FileGroup where vFileGroupID='".$fileGroupId."';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);
        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $row = DatabaseManager::mysql_fetch_array($result);
                $this->fileGroup = new FileGroup(
                    $row["vFileGroupCode"],
                    $row["vName"],
                    $row["tDescription"],
                    $row["dCreateDate"],
                    $row["dLastUpdate"],
                    $row["cStatus"]
                );
            } else {
                // $response = "Error! Session expired";
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "FileGroupHandler->getFileGroup; Empty resultset for code '".$fileGroupId."'",
                    "The file group requested does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "FileGroupHandler->getFileGroup; Null resultst: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->fileGroup;
    }


    public function deleteFileGroup($fileGroupCode)
    {
        $error = "";
        $errorNodeFactory = new ErrorNodeFactory();
        $queryHolder="SET @sID=''";
        $querySelectHolder="SELECT @sID AS 's_id'";
        $query = "CALL prnUpdFileGroup('".$fileGroupCode.
        "','1',@sID);";
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
                                "Class:FileGroupHandler->deleteFileGroup; Empty session id returned ",
                                "Unable to delete file group.<br/>Please try again Later"
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:FileGroupHandler->deleteFileGroup; Empty result set returned",
                            "Unable to delete file group.<br/>Please try again Later"
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "FileGroupHandler->deleteFileGroup; Null resultset: ".
                        DatabaseManager::mysql_error(),
                        "Unable to delete file group.<br/>Please try again Later"
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:FileGroupHandler->deleteFileGroup; Query failed: ".
                    DatabaseManager::mysql_error(),
                    "Unable to delete file group.<br/>Please try again Later"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:FileGroupHandler->deleteFileGroup; Holder failed: ".
                DatabaseManager::mysql_error(),
                "Unable to delete file group.<br/>Please try again Later"
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->status;
    }
}
