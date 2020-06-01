<?php
namespace Lib\EmailManager;

use Lib\EmailManager\EmailContent;
use Lib\ErrorReporter\ErrorReporter;
use Lib\ErrorReporter\ErrorNodeFactory;

class SignupEmailContent implements EmailContent
{
    private $senderEmail = "buildforsdg@pulchrablog.com";
    private $senderName = "Broomy.com";
    private $receiverEmail;
    private $subject;
    private $body;
    private $bodyType = "plain";
    private $carbonCopy;
    private $blindCopy;
    private $userFullName;
    private $password;


    public function __construct($userFullName, $receiverEmail, $password)
    {
        $this->setUserFullName($userFullName);
        $this->setReceiverEmail($receiverEmail);
        $this->setPassword($password);
        $this->setSubject("Broomy - Welcome ".$userFullName);
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

    public function setReceiverEmail($receiverEmail)
    {
        $this->receiverEmail = $receiverEmail;
    }

    public function getReceiverEmail()
    {
        return $this->receiverEmail;
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
                $this->initializeHTMLBody();
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
                "SignupEmail->initializeHTMLBody; userfuLLName: "
                .$this->userFullName.
                "|password: ".$this->password."; is null",
                "Internal error occured. Please try again later"
            );
            ErrorReporter::addNode($errorNode);
            $this->body = null;
            return;
        }

        $this->body = "Hi ".$this->userFullName."<br/>"."Here are your login details username: ".
        $this->receiverEmail."<br/>Password: ".$this->password;
    }

    private function initializePlainTextBody()
    {
        $errorNodeFactory = new ErrorNodeFactory();
        if ($this->body === null ||
        $this->userFullName === null ||
        $this->password === null) {
            $errorNode = $errorNodeFactory->createObjectError(
                "SignupEmail->initializeHTMLBody; body|userFullName|password is null",
                "Internal error occured. Please try again later"
            );
            ErrorReporter::addNode($errorNode);
            $this->body = null;
        } else {
            $this->body = "Hi ".$this->userFullName."\n"."Here are your login details username: ".
        $this->userName."\nPassword: ".$this->password;
        }
    }

    public function __toString()
    {
        return "senderName: ".$this->senderName.", SenderEmail: ".
        $this->senderEmail.", receiverEmail: ".$this->receiverEmail.
        ", subject: ".$this->subject.", body: ".
        $this->body.", user Full name: ".
        $this->userFullName.", password: ".$this->password;
    }
}
