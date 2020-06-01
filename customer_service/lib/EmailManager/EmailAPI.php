<?php
namespace Lib\EmailManager;

interface EmailAPI
{
    public function setConnection($connection);
    public function getConnection();
    public function setEmailContent($email);
    public function getEmailContent();
    public function sendEmail();
}
