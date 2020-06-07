<?php
namespace Lib\EmailManager;

use Lib\EmailManager\SMTPEmailConnection;

class SMTPEmailConnectionFactory
{
    public function __construct()
    {
    }

    public function createBroomySMTPEmailConnection()
    {
        return new SMTPEmailConnection("smtp.ipage.com", "buildforsdg@pulchrablog.com", "Team-064", 'UTF-8', 0, true, 465);
    }

    public function createDefaultSMTPEmailConnection()
    {
        return $this->createBroomySMTPEmailConnection();
    }
}
