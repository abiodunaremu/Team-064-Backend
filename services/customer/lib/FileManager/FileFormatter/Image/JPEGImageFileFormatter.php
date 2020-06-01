<?php
namespace Lib\FileManager\FileFormatter\Image;

use Lib\FileManager\FileFormatter\FileFormatter;
use Lib\FileManager\FileType\FileTypeHandler;

class JPEGImageFileFormatter implements FileFormatter
{
    protected $tempPath = BASEPATH."/uploads/temp/jpeg/";
    protected $fileTypeName ="IMAGE/JPEG";
    protected $fileType;
    protected $sourceFile;
    protected $fileCreator;

    public function __construct()
    {
        $this->tempPath = str_replace('\\', '/', BASEPATH."/uploads/temp/jpeg/");
        
        if ($this->fileType == null) {
            $this->getFileType();
        }
    }
    
    public function setTempPath($tempPath)
    {
        $this->tempPath = $tempPath;
        return $this;
    }
    public function getTempPath()
    {
        return $this->tempPath;
    }

    public function setSourceFile($sourceFile)
    {
        $this->sourceFile = $sourceFile;
        return $this;
    }
    public function getSourceFile()
    {
        return $this->sourceFile;
    }
    
    public function setFileCreator($fileCreator)
    {
        $this->fileCreator = $fileCreator;
        return $this;
    }
    public function getFileCreator()
    {
        return $this->fileCreator;
    }

    public function isValid()
    {
        if ($this->fileType == null) {
            $this->getFileType();
        }
        if (in_array(
            strtoupper($this->getSourceFile()->getExtension()),
            explode(",", strtoupper($this->fileType->getExtension()))
        )) {
            return true;
        } else {
            return false;
        }
    }

    public function getFileType()
    {
        $fileTypeHandler = new FileTypeHandler();
        $this->fileType = $fileTypeHandler->getFileTypeByName($this->fileTypeName);
        return $this->fileType;
    }
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
        return $this;
    }
    
    public function isInitialized()
    {
        if ($this->sourceFile == null || $this->sourceFile === ''
        || $this->tempPath == null || $this->tempPath === ''
        || $this->fileType == null || $this->fileType === ''
        || $this->fileCreator == null || $this->fileCreator === '') {
            return false;
        } else {
            return true;
        }
    }
}
