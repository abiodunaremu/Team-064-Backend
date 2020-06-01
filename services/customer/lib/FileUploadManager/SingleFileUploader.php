<?php
namespace Lib\FileUploadManager;

use Lib\FileUploadManager\FileUploader;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;
use Lib\FileManager\FileGroup\FileGroupHandler;
use Lib\FileManager\FileCreator\FileCreatorHandler;
use Lib\FileManager\FileType\FileTypeHandler;
use Lib\FileManager\File\PersistFileHandler;
use Lib\FileManager\File\LogicFile;
use Lib\FileManager\File\PersistFile;

/** Concrete class to manage single file uploading */
class SingleFileUploader implements FileUploader
{
    private $fileGroupId;

    public function getName()
    {
        return "Single_File_Uploader";
    }

    public function upload($sessionId, $tempFile, $fileFormatter)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $fileUploaded = false;

        $tempDestinationFile = new LogicFile(
            $sessionId
            .$fileFormatter->getSourceFile()
            ->getName(),
            $fileFormatter->getSourceFile()->getExtension(),
            $fileFormatter->getTempPath(),
            $fileFormatter->getSourceFile()->getSize()
        );

        if ($fileFormatter->isInitialized()
        && $fileFormatter->isValid()) {
            $fileUploaded = move_uploaded_file(
                $tempFile->getURL(),
                $tempDestinationFile->getURL()
            );
        } else {
            $errorNode = $errorNodeFactory->createObjectError(
                "SingleFileUploader->upload; FileFormatter not initialized or invalid",
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        if ($fileUploaded) {

            //persist file group
            $fileGroupHandler = new FileGroupHandler();
            $this->fileGroupId = $fileGroupHandler
            ->persistFileGroup($sessionId, $fileFormatter
            ->getFileCreator()->getId());

            //persist temp destination file
            $fileHandler = new PersistFileHandler();
            // $temp_fileId = $fileHandler->persistFile($sessionId
            // , $fileGroupId
            // , $fileType->getName()
            // , $fileFormatter->getSourceFile()->getName()
            // , $fileFormatter->getTempPath()
            // , $fileFormatter->getSourceFile()->getExtension()
            // , $fileFormatter->getSourceFile()->getSize());

            //persist production destination file
            $production_fileId = $fileHandler->persistFile($sessionId, $this->fileGroupId, $fileFormatter->getFileType()->getName(), $fileFormatter->getSourceFile()->getName(), $fileFormatter->getFileCreator()->getFileDestinationPath(), $fileFormatter->getSourceFile()->getExtension(), $fileFormatter->getSourceFile()->getSize());

            $destinationFileUrl = $fileFormatter
            ->getFileCreator()->getFileDestinationPath().$production_fileId."."
            .$fileFormatter->getSourceFile()->getExtension();
            
            $productionFile = new LogicFile(
                $production_fileId,
                $fileFormatter->getSourceFile()->getExtension(),
                $fileFormatter->getFileCreator()->getFileDestinationPath(),
                $fileFormatter->getSourceFile()->getSize()
            );

            //move temp destination file to appropriate production destination with new name
            rename($tempDestinationFile->getURL(), $productionFile->getURL());
        } else {
            $errorNode = $errorNodeFactory->createObjectError(
                "SingleFileUploader->upload; upload failed"
            .$sessionId."-TempFile>>".$tempFile->getUrl()
            ."-DestinationFile>>".$tempDestinationFile->getUrl(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->fileGroupId;
    }
}
