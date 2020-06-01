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

/** Concrete class to manage multiple file uploading */
class MultipleFileUploader implements FileUploader
{
    private $fileGroupId;

    public function getName()
    {
        return "multiple_File_Uploader";
    }

    public function upload($sessionId, $tempFiles, $validFileFormatters)
    {
        $errorNodeFactory = new ErrorNodeFactory();
        $fileUploaded = [];
        $tempDestinationFile = [];

        foreach ($validFileFormatters as $key => $fileFormatter) {
            $tempDestinationFile[$key] = new LogicFile(
                $sessionId
                .$fileFormatter->getSourceFile()
                ->getName(),
                $fileFormatter->getSourceFile()->getExtension(),
                $fileFormatter->getTempPath(),
                $fileFormatter->getSourceFile()->getSize()
            );


            if ($fileFormatter->isInitialized()) {
                $fileUploaded[$key] = move_uploaded_file(
                    $tempFiles[$key]->getURL(),
                    $tempDestinationFile[$key]->getURL()
                );
            } else {
                $errorNode = $errorNodeFactory->createObjectError(
                    "MultipleFileUploader->upload; FileFormatter not initialized or invalid",
                    "Internal error occured. Please try again later."
                );
                ErrorReporter::addNode($errorNode);
            }
        }


        if (!in_array(false, $fileUploaded)) {

            //persist file group
            $fileGroupHandler = new FileGroupHandler();
            $this->fileGroupId = $fileGroupHandler
            ->persistFileGroup($sessionId, $fileFormatter
            ->getFileCreator()->getId());

            foreach ($validFileFormatters as $key => $fileFormatter) {
                //persist production destination file
                $fileHandler = new PersistFileHandler();
                $production_fileId = $fileHandler->persistFile(
                    $sessionId,
                    $this->fileGroupId,
                    $fileFormatter->getFileType()->getName(),
                    $fileFormatter->getSourceFile()->getName(),
                    $fileFormatter->getFileCreator()->getFileDestinationPath(),
                    $fileFormatter->getSourceFile()->getExtension(),
                    $fileFormatter->getSourceFile()->getSize()
                );

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
                rename($tempDestinationFile[$key]->getURL(), $productionFile->getURL());
            }
        } else {
            $errorNode = $errorNodeFactory->createObjectError(
                "MultipleFileUploader->upload; upload failed"
            .$sessionId."->".sizeof($tempFiles)."->>".$tempFiles[0]->getName()
            ."->>".$tempDestinationFile[0]->getName()
            ."<->".sizeof($validFileFormatters)."->"."->". $validFileFormatters[0]->getFileType()->getName(),
                "Internal error occured. Please try again later."
            );
            ErrorReporter::addNode($errorNode);
        }
        return $this->fileGroupId;
    }
}
