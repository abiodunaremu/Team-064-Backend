<?php
namespace Lib\FileUploadManager;

interface FileUploader
{
    public function upload($sessionId, $sourceLogicFile, $fileFormatter);
}
