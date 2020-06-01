<?php
namespace Lib\FileManager\File;

use Lib\FileManager\File\File;

class PersistFile implements File
{
    private $id;
    private $name;
    private $fileGroupId;
    private $fileTypeId;
    private $fileTypeName;
    private $oldName;
    private $extension;
    private $path;
    private $size;
    private $createdTime;
    private $lastUpdate;
    private $status;

    public function __construct($id, $fileGroupId, $fileTypeId, $fileTypeName, $oldName, $extension, $path, $size, $createdTime, $lastUpdateTime, $status)
    {
        $this->id = $id;
        $this->name = $id;
        $this->fileGroupId = $fileGroupId;
        $this->fileTypeId = $fileTypeId;
        $this->fileTypeName = $fileTypeName;
        $this->oldName = $oldName;
        $this->extension = $extension;
        $this->path = $path;
        $this->size = $size;
        $this->createdTime = $createdTime;
        $this->lastUpdateTime = $lastUpdateTime;
        $this->status = $status;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->id;
    }

    public function setName($name)
    {
        // $this->name = $name;
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
    
    public function setFileTypeName($fileTypeName)
    {
        $this->fileTypeName = $fileTypeName;
    }
    public function getFileTypeName()
    {
        return $this->fileTypeName;
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
    
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;
    }
    
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    public function setLastUpdatedTime($lastUpdatedTime)
    {
        $this->lastUpdatedTime = $lastUpdatedTime;
    }
    
    public function getLastUpdatedTime()
    {
        return $this->lastUpdatedTime;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    

    public function getURL()
    {
        return $this->path.$this->name.".".$this->extension;
    }
}
