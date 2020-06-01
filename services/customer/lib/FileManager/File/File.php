<?php
namespace Lib\FileManager\File;

interface File
{
    public function setName($name);
    public function getName();
    public function setPath($path);
    public function getPath();
    public function setSize($size);
    public function getSize();
    public function setExtension($extension);
    public function getExtension();
    public function getURL();
}
