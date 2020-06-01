<?php
namespace Lib\FileUploadManager;

use Lib\FileUploadManager\SingleFileUploader;
use Lib\FileUploadManager\MultipleFileUploader;

class FileUploadManager
{
    public function createSingleFileUploader()
    {
        return new SingleFileUploader();
    }
    public function createMultipleFileUploader()
    {
        return new MultipleFileUploader();
    }
}
