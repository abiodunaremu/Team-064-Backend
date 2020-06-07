<?php
namespace Lib\FileManager\FileFormatter;

use Lib\FileManager\FileFormatter\Image\ImageFileFormatter;
use Lib\FileManager\FileFormatter\Image\JPEGImageFileFormatter;

class FileFormatterFactory
{
    public function createJPEGImageFileFormatter($sourceFile, $fileCreator)
    {
        $JPEGImageFileFormatter = new JPEGImageFileFormatter();
        $JPEGImageFileFormatter->setFileCreator($fileCreator);
        $JPEGImageFileFormatter->setSourceFile($sourceFile);
        return $JPEGImageFileFormatter;
    }

    // public function createImageFileFormatter($sourceFile, $fileCreator)
    // {
    //     $imageFileFormatter = new ImageFileFormatter();
    //     $imageFileFormatter->setSourceFile($sourceFile);
    //     $imageFileFormatter->setFileCreator($fileCreator);
    //     return $imageFileFormatter;
    // }
}
