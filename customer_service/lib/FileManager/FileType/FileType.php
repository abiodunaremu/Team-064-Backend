<?php
namespace Lib\FileManager\FileType;

/** A model persisted fileType */
class FileType
{
    private $id;
    private $name;
    private $description;
    private $extension;
    private $createdTime;
    private $lastUpdateTime;
    private $status;

    public function __construct($id, $name, $description, $extension, $createdTime, $lastUpdateTime, $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->extension = $extension;
        $this->createdTime = $createdTime;
        $this->lastUpdateTime = $lastUpdateTime;
        $this->status = $status;
    }

    public function getId()
    {
        return $this->Id;
    }

    public function setId($Id)
    {
        $this->Id = $Id;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }
    
    public function getExtension()
    {
        return $this->extension;
    }
    
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;
    }
    
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    public function setLastUpdatedTime($lastUpdateTime)
    {
        $this->lastUpdateTime = $lastUpdateTime;
    }
    
    public function getLastUpdatedTime()
    {
        return $this->lastUpdateTime;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
    
    public function getStatus()
    {
        return $this->status;
    }

    public function toString()
    {
        return $this->id.">".$this->name.">".$this->description;
    }
}
