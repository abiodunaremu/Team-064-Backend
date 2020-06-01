<?php
namespace Lib\FileManager\File;

use Lib\FileManager\File\File;

class LogicFile implements File
{
    private $name;
    private $extension;
    private $path;
    private $size;

    public function __construct($name, $extension, $path, $size)
    {
        $this->name = $name;
        $this->extension = $extension;
        $this->path = $path;
        $this->size = $size;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getFileGroupId()
    {
        return $this->fileGroupId;
    }

    public function setFileGroupId($fileGroupId)
    {
        $this->fileGroupId = $fileGroupId;
    }
    
    public function setFileTypeId($fileTypeId)
    {
        $this->fileTypeId = $fileTypeId;
    }
    
    public function getFileTypeId()
    {
        return $this->fileTypeId;
    }
    
    public function setOldName($oldName)
    {
        $this->oldName = $oldName;
    }
    
    public function getOldName()
    {
        return $this->oldName;
    }
    
    public function setPath($path)
    {
        $this->path = $path;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }
    
    public function getExtension()
    {
        return $this->extension;
    }
    
    public function setSize($size)
    {
        $this->size = $size;
    }
    
    public function getSize()
    {
        return $this->size;
    }

    public function getURL()
    {
        return ($this->extension !== null && $this->extension !== "") ?
        $this->path.$this->name.".".$this->extension
        : $this->path.$this->name;
    }
}
