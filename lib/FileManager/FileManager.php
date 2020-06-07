<?php
namespace Lib\FileManager;

use Lib\FileManager\File\LogicFile;
use Lib\FileManager\File\PersistFileHandler;

class FileManager
{
    public function createLogicFile($name, $extension, $path, $size)
    {
        return new LogicFile($name, $extension, $path, $size);
    }
    
    public function createPersistFileHandler()
    {
        return new PersistFileHandler();
    }
}
