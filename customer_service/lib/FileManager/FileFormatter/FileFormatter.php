<?php
namespace Lib\FileManager\FileFormatter;

interface FileFormatter
{
    public function setSourceFile($sourceFile);
    public function getSourceFile();
    public function setTempPath($path); //in
    public function getTempPath();
    public function setFileType($fileType); //in
    public function getFileType();
    public function setFileCreator($fileCreator); //in
    public function getFileCreator();
    public function isInitialized();
    public function isValid();
}
