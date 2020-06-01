<?php
namespace Lib\FileManager\FileType;

use Lib\Database\DatabaseManager;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;
use Lib\FileManager\FileType\FileType;

class FileTypeHandler
{
    private $fileType;
    private $fileTypes;
    private $status;

    public function __construct()
    {
    }

    public function persistFileType($name, $description, $extension)
    {
        $error = "";
        $response = "";
        $errorNodeFactory = new ErrorNodeFactory();
        $queryHolder="SET @sID=''";
        $querySelectHolder="SELECT @sID AS 's_id'";
        $query = "CALL prnInsFileType('".$name."', '".$description
        ."', '".$extension."', @sID);";
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
                                "Class:FileTypeHandler->persistFileType; Empty status returned ",
                                "Unable to register file type.<br/>Please try again Later"
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:FileTypeHandler->persistFileType; Empty status returned ",
                            "Unable to register file type.<br/>Please try again Later"
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "Class:FileTypeHandler->persistFileType; Null resultset: ".
                        DatabaseManager::mysql_error(),
                        "Unable to register file type.<br/>Please try again Later"
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:FileTypeHandler->persistFileType; Query failed: ".
                    DatabaseManager::mysql_error(),
                    "Unable to register file type.<br/>Please try again Later"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:FileTypeHandler->persistFileType; Holder failed: ".
                DatabaseManager::mysql_error(),
                "Unable to register file type.<br/>Please try again Later"
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->status;
    }

    public function getFileTypes()
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM FileType WHERE cStatus='0';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);

        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $this->fileTypes = [];
                for ($x = 0; $x < $num_results; $x++) {
                    $row = DatabaseManager::mysql_fetch_array($result);
                    array_push($this->fileTypes, new FileType(
                        $row["iFileTypeID"],
                        $row["vName"],
                        $row["tDescription"],
                        $row["vExtension"],
                        $row["dCreateDate"],
                        $row["dLastUpdate"],
                        $row["cStatus"]
                    ));
                }
            } else {
                // $response = "Error! Session expired";
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "FileTypeHandler->getFileTypes; Empty resultset",
                    "Job type does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "FileTypeHandler->getFileTypes; Null resultst: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }

        return $this->fileTypes;
    }

    public function getFileTypeById($fileTypeCode)
    {
        return $this->getFileType($fileTypeCode);
    }
    
    protected function getFileType($fileTypeCode)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM FileType where iFileTypeID='".$fileTypeCode."';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);
        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $row = DatabaseManager::mysql_fetch_array($result);
                $this->fileType = new FileType(
                    $row["iFileTypeID"],
                    $row["vName"],
                    $row["tDescription"],
                    $row["vExtension"],
                    $row["dCreateDate"],
                    $row["dLastUpdate"],
                    $row["cStatus"]
                );
            } else {
                // $response = "Error! Session expired";
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "FileTypeHandler->getFileType; Empty resultset for code '".$fileTypeCode."'",
                    "The file type requested does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "FileTypeHandler->getFileType; Null resultst: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->fileType;
    }

    public function getFileTypeByName($fileTypeName)
    {
        return $this->getFileTypeDetails($fileTypeName);
    }
    
    protected function getFileTypeDetails($fileTypeName)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $query = "SELECT * FROM FileType where vName='".$fileTypeName."';";
        $row="";
        $response="";
        $error="";
        $result = DatabaseManager::mysql_query($query);
        if ($result) {
            $num_results = DatabaseManager::mysql_num_rows($result);
            if ($num_results>0) {
                $row = DatabaseManager::mysql_fetch_array($result);
                $this->fileType = new FileType(
                    $row["iFileTypeID"],
                    $row["vName"],
                    $row["tDescription"],
                    $row["vExtension"],
                    $row["dCreateDate"],
                    $row["dLastUpdate"],
                    $row["cStatus"]
                );
            } else {
                // $response = "Error! Session expired";
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "FileTypeHandler->getFileType; Empty resultset for code '".$fileTypeName."'",
                    "The file type requested does not exist"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "FileTypeHandler->getFileType; Null resultst: ".DatabaseManager::mysql_error(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->fileType;
    }


    public function deleteFileType($fileTypeCode)
    {
        $error = "";
        $errorNodeFactory = new ErrorNodeFactory();
        $queryHolder="SET @sID=''";
        $querySelectHolder="SELECT @sID AS 's_id'";
        $query = "CALL prnUpdFileType('".$fileTypeCode.
        "','','',1',@sID);";
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
                                "Class:FileTypeHandler->deleteFileType; Empty session id returned ",
                                "Unable to delete file type.<br/>Please try again Later"
                            );
                            ErrorReporter::addNode($errorNode);
                        }
                    } else {
                        $errorNode = $errorNodeFactory->createPersistenceError(
                            "Class:FileTypeHandler->deleteFileType; Empty result set returned",
                            "Unable to delete file type.<br/>Please try again Later"
                        );
                        ErrorReporter::addNode($errorNode);
                    }
                } else {
                    $errorNode = $errorNodeFactory->createPersistenceError(
                        "FileTypeHandler->deleteFileType; Null resultset: ".
                        DatabaseManager::mysql_error(),
                        "Unable to delete file type.<br/>Please try again Later"
                    );
                    ErrorReporter::addNode($errorNode);
                }
            } else {
                $errorNode = $errorNodeFactory->createPersistenceError(
                    "Class:FileTypeHandler->deleteFileType; Query failed: ".
                    DatabaseManager::mysql_error(),
                    "Unable to delete file type.<br/>Please try again Later"
                );
                ErrorReporter::addNode($errorNode);
            }
        } else {
            $errorNode = $errorNodeFactory->createPersistenceError(
                "Class:FileTypeHandler->deleteFileType; Holder failed: ".
                DatabaseManager::mysql_error(),
                "Unable to delete file type.<br/>Please try again Later"
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->status;
    }
}
