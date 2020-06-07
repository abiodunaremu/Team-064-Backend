<?php
namespace Lib\EmailManager;

interface EmailContent
{
    public function setSubject($setSubject);
    public function getSubject();
    public function setSenderEmail($senderEmail);
    public function getSenderEmail();
    public function setSenderName($senderName);
    public function getSenderName();
    public function setReceiverEmail($receiver);
    public function getReceiverEmail();
    public function setCarbonCopy($carbonCopy);
    public function getCarbonCopy();
    public function setBody($body);
    public function getBody();
    public function setBodyType($bodyType);
    public function getBodyType();
    public function setBlindCopy($setSubject);
    public function getBlindCopy();
}
