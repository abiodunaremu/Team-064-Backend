<?php

namespace Lib\EmailManager;
/** The builder class for sending emails */

use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;
use Lib\EmailManager\PHPMailerEmailAPI;
use Lib\EmailManager\SMTPEmailConnectionFactory;

class EmailManager
{
    private $emailContent;
    private $emailConnection;
    private $emailAPI;
    private $smtpEmailConnectionFactory;

    public function __construct()
    {
        $this->smtpEmailConnectionFactory = new SMTPEmailConnectionFactory();
    }

    public function setEmailContent($emailContent)
    {
        $this->emailContent = $emailContent;
        return $this;
    }

    //Sets the emailContent content
    public function getEmailEmail()
    {
        return $this->emailContent;
    }

    //Sets the emailContent emailConnection details
    public function setConnection($emailConnection)
    {
        $this->emailConnection = $emailConnection;
        return $this;
    }

    //Sets the emailContent emailConnection to predefined default SMTPConnection
    public function useDefaultSMTPEmailConnection()
    {
        $this->emailConnection = $this->smtpEmailConnectionFactory
        ->createDefaultSMTPEmailConnection();
        return $this;
    }

    public function getConnection()
    {
        return $this->emailConnection;
    }

    //Sends this emailContent with preferred Email API
    public function setEmailAPI($emailAPI)
    {
        $emailAPI->setEmailContent($this->emailContent);
        $emailAPI->setConnection($this->emailConnection);
        $this->emailAPI = $emailAPI;
        return $this;
    }

    public function getEmailAPI()
    {
        return $this->emailAPI;
    }

    //Sets the PHPMailerAPI to predefined EmailAPI
    public function usePHPMailerEmailAPI()
    {
        $emailAPI = new PHPMailerEmailAPI();
        $emailAPI->setEmailContent($this->emailContent);
        $emailAPI->setConnection($this->emailConnection);
        $this->emailAPI = $emailAPI;
        return $this;
    }

    //Sends this emailContent with preferred Email API
    public function sendEmail()
    {
        $errorNodeFactory = new ErrorNodeFactory();

        if ($this->emailConnection === null ||
        $this->emailContent === null||
        $this->emailAPI === null) {
            $errorNode = $errorNodeFactory->createObjectError(
                "EmailManager->sendEmail; Null value in emailConnection or emailContent: <br/> emailConnection; "
                .$this->emailConnection.
                "<br/> || emailContent; ".$this->emailContent.
                "<br/> || emailAPI; ".$this->emailAPI,
                "Internal error occured while sending email. Please try again later"
            );
            ErrorReporter::addNode($errorNode);
            return;
        }

        return $this->emailAPI->sendEmail();
    }
}