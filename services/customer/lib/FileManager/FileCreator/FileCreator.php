<?php
namespace Lib\FileManager\FileCreator;

class FileCreator
{
    private $id;
    private $name;
    private $description;
    private $fileDestinationPath;
    private $createdTime;
    private $lastUpdate;
    private $status;

    public function __construct($id, $name, $description, $fileDestinationPath, $createdTime, $lastUpdateTime, $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->fileDestinationPath = $fileDestinationPath;
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
    
    public function setFileDestinationPath($fileDestinationPath)
    {
        $this->fileDestinationPath = $fileDestinationPath;
    }
    
    public function getFileDestinationPath()
    {
        return str_replace(
            '\\',
            '/',
            BASEPATH.$this->fileDestinationPath
        );
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
}
