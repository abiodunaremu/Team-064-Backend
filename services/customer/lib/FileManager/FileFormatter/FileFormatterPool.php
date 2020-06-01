<?php
namespace Lib\FileManager\FileFormatter;

class FileFormatterPool
{
    private $fileFormatters = [];
    private $fileFormatter;
    private $sourceFile;

    //add a type of acceptable image file formatter to list
    public function addFileFormatter($fileFormatter)
    {
        array_push($this->fileFormatters, $fileFormatter);
    }

    //accept source file (logicfile)
    public function setSourceFile($sourceFile)
    {
        $this->sourceFile = $sourceFile;
    }

    //check if source file matches any of the accepted file format
    public function getValidFileFormatter()
    {
        if ($this->fileFormatters != null
        && sizeof($this->fileFormatters) > 0
        && $this->sourceFile != null && $this->sourceFile !== '') {
            foreach ($this->fileFormatters as $fileFormatter) {
                $fileFormatter->setSourceFile($this->sourceFile);
                if ($fileFormatter->isValid()) {
                    $this->fileFormatter = $fileFormatter;
                    break;
                }
            }
            return $this->fileFormatter;
        }
    }
}
