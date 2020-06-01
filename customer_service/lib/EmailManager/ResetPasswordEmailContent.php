<?php
namespace Lib\EmailManager;

use Lib\EmailManager\EmailContent;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;

class ResetPasswordEmailContent implements EmailContent
{
    private $senderEmail = "buildforsdg@pluralblog.com";
    private $senderName = "Broomy.com";
    private $userEmail;
    private $subject;
    private $body;
    private $bodyType = "HTML";
    private $carbonCopy;
    private $blindCopy;
    private $userFullName;
    private $password;


    public function __construct($userFullName, $userEmail, $password)
    {
        $this->setUserFullName($userFullName);
        $this->setReceiverEmail($userEmail);
        $this->setPassword($password);
        $this->setSubject($userFullName.", your password has been reset");
    }

    public function setUserFullName($userFullName)
    {
        $this->userFullName = $userFullName;
    }

    public function getUserFullName()
    {
        return $this->userFullName;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setReceiverEmail($userEmail)
    {
        $this->userEmail = $userEmail;
    }

    public function getReceiverEmail()
    {
        return $this->userEmail;
    }

    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }

    public function getSenderName()
    {
        return $this->senderName;
    }

    public function setBody($body)
    {
        $this->body;
    }

    public function getBody()
    {
        //initialize the body with user's name and reciever email
        switch ($this->bodyType) {
        case "plainText":
            $this->initializePlainTextBody();
            // no break
        case "HTML":
            // $this->initializeHTMLBody();
            $this->initializePlainTextBody();
            // no break
        default:
            $this->initializeHTMLBody();
    }
        return $this->body;
    }

    public function setBodyType($bodyType)
    {
        $this->bodyType;
    }

    public function getBodyType()
    {
        return $this->bodyType;
    }

    public function setCarbonCopy($carbonCopy)
    {
        $this->carbonCopy;
    }

    public function getCarbonCopy()
    {
        return $this->carbonCopy;
    }

    public function setBlindCopy($blindCopy)
    {
        $this->blindCopy;
    }

    public function getBlindCopy()
    {
        return $this->blindCopy;
    }

    private function initializeHTMLBody()
    {
        $errorNodeFactory = new ErrorNodeFactory();
        if ($this->userFullName === null ||
    $this->password === null) {
            $errorNode = $errorNodeFactory->createObjectError(
                "ResetPasswordEmailContent->initializeHTMLBody; userfuLLName: "
            .$this->userFullName.
            "|password: ".$this->password."; is null",
                "Internal error occured. Please try again later"
            );
            ErrorReporter::addNode($errorNode);
            $this->body = null;
            return;
        }

        $this->body = "Hi ".$this->userFullName.",<br/>".
        "Your account password has been reset.<br/>New Password= ".
        $this->password;
    }

    private function initializePlainTextBody()
    {
        $errorNodeFactory = new ErrorNodeFactory();
        if ($this->body === null ||
    $this->userFullName === null ||
    $this->password === null) {
            $errorNode = $errorNodeFactory->createObjectError(
                "ResetPasswordEmailContent->initializeHTMLBody; body|userFullName|password is null",
                "Internal error occured. Please try again later"
            );
            ErrorReporter::addNode($errorNode);
            $this->body = null;
        } else {
            $this->body = "Hi ".$this->userFullName.",\n".
        ",\nYour account password has been reset.\nNew Password= ".
        $this->password;
        }
    }

    public function __toString()
    {
        return "senderName: ".$this->senderName.", SenderEmail: ".
    $this->senderEmail.", receiverEmail: ".$this->userEmail.
    ", subject: ".$this->subject.", body: ".
    $this->body.", user Full name: ".
    $this->userFullName.", password: ".$this->password;
    }
}
