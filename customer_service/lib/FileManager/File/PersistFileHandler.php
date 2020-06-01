<?php
namespace Lib\FileManager\File;

use Lib\Database\DatabaseManager;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;

class PersistFileHandler
{
    private $file;
    private $files;
    private $status;

    public function __construct()
    {
    }

    public function persistFile($sessionId, $fileGroupId, $fileType, $oldName, $fileDirPath, $extension, $size)
    {
        $error = "";
        $response = "";
        $errorNodeFactory = new ErrorNodeFactory();
        $queryHolder="SET @sID=''";
        $querySelectHolder="SELECT @sID AS 's_id'";
        $query = "CALL prnInsFile('".$sessionId."', '"
        .$fileGroupId."', '".$fileType."', '".$oldName
        ."', '".$fileDirPath."', '".$extension."', '".$size."', @sID);";

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
                                "Class:FileHandler->persistFile; Empty status returned ",
                                "Unable to register file.<br/>Please try again Later"
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:FileHandler->persistFile; Empty status returned ",
                            "Unable to register file.<br/>Please try again Later"
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "Class:FileHandler->persistFile; Null resultset: ".
                        DatabaseManager::mysql_error(),
                        "Unable to register file.<br/>Please try again Later"
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:FileHandler->persistFile; Query failed: ".
                    DatabaseManager::mysql_error(),
                    "Unable to register file.<br/>Please try again Later"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:FileHandler->persistFile; Holder failed: ".
                DatabaseManager::mysql_error(),
                "Unable to register file.<br/>Please try again Later"
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->status;
    }

    public function getPersistFiles($fileGroupId)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM vwFile WHERE vFileGroupID='".$fileGroupId."' AND cStatus='0';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);

        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $this->files = [];
                for ($x = 0; $x < $num_results; $x++) {
                    $row = DatabaseManager::mysql_fetch_array($result);
                    array_push($this->files, new PersistFile(
                        $row["vFileID"],
                        $row["vFileGroupID"],
                        $row["iFileTypeID"],
                        $row["vFileTypeName"],
                        $row["vOldName"],
                        $row["vExtension"],
                        $row["tPath"],
                        $row["dSize"],
                        $row["dCreateDate"],
                        $row["dLastUpdate"],
                        $row["cStatus"]
                    ));
                }
            } else {
                // $response = "Error! Session expired";
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "FileHandler->getFiles; Empty resultset",
                    "File does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "FileHandler->getFiles; Null resultst: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }

        return $this->files;
    }

    public function getPersistFileById($sessionId, $fileId)
    {
        return $this->getPersistFile($sessionId, $fileId);
    }
    
    protected function getPersistFile($sessionId, $fileId)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM vwFile where vFileID='".$fileId."';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);
        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $row = DatabaseManager::mysql_fetch_array($result);
                $this->file = new PersistFile(
                    $row["vFileID"],
                    $row["vFileGroupID"],
                    $row["iFileTypeID"],
                    $row["vFileTypeName"],
                    $row["vOldName"],
                    $row["vExtension"],
                    $row["tPath"],
                    $row["dSize"],
                    $row["dCreateDate"],
                    $row["dLastUpdate"],
                    $row["cStatus"]
                );
            } else {
                // $response = "Error! Session expired";
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "FileHandler->getFile; Empty resultset for code '".$fileId."'",
                    "The file requested does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "FileHandler->getFile; Null resultst: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->file;
    }


    public function deleteFile($fileId)
    {
        $error = "";
        $errorNodeFactory = new ErrorNodeFactory();
        $queryHolder="SET @sID=''";
        $querySelectHolder="SELECT @sID AS 's_id'";
        $query = "CALL prnUpdFile('".$fileId.
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
                                "Class:FileHandler->deleteFile; Empty session id returned ",
                                "Unable to delete file.<br/>Please try again Later"
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:FileHandler->deleteFile; Empty result set returned",
                            "Unable to delete file.<br/>Please try again Later"
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "FileHandler->deleteFile; Null resultset: ".
                        DatabaseManager::mysql_error(),
                        "Unable to delete file.<br/>Please try again Later"
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:FileHandler->deleteFile; Query failed: ".
                    DatabaseManager::mysql_error(),
                    "Unable to delete file.<br/>Please try again Later"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:FileHandler->deleteFile; Holder failed: ".
                DatabaseManager::mysql_error(),
                "Unable to delete file.<br/>Please try again Later"
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->status;
    }
}
