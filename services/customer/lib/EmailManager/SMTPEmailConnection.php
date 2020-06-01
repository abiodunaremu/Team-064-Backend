<?php
namespace Lib\EmailManager;

/** SMTPEmailConnection class sets all attributes
 * required to connect to an email server
 * This includes the port, ipaddress, username and password */

use Lib\EmailManager\EmailConnection;

class SMTPEmailConnection implements EmailConnection
{
    private $HostURL;
    private $username;
    private $password;
    private $port;
    private $CharSet;
    private $SMTPDebug;
    private $SMTPAuth;

    public function __construct($hostUrl, $userName, $password, $charSet, $SMTPDebug, $SMTPAuth, $port)
    {
        $this->setHostURL($hostUrl);
        $this->setUsername($userName);
        $this->setPassword($password);
        $this->setCharSet($charSet);
        $this->setSMTPDebug($SMTPDebug);
        $this->setSMTPAuth($SMTPAuth);
        $this->setPort($port);
    }

    public function setSMTPAuth($SMTPAuth)
    {
        $this->$SMTPAuth = $SMTPAuth;
    }

    //returns SMTPDEBUG
    public function getSMTPAuth()
    {
        return $this->SMTPAuth;
    }

    public function setSMTPDebug($SMTPDebug)
    {
        $this->$SMTPDebug = $SMTPDebug;
    }

    //returns SMTPDEBUG
    public function getSMTPDebug()
    {
        return $this->SMTPDebug;
    }
    
    public function setCharSet($CharSet)
    {
        $this->$CharSet = $CharSet;
    }

    //returns the set Charset
    public function getCharSet()
    {
        return $this->CharSet;
    }
    
    //set connection ipaddress, port and url
    public function setHostURL($HostURL)
    {
        $this->HostURL = $HostURL;
    }

    //returns the set email URL
    public function getHostURL()
    {
        return $this->HostURL;
    }

    //sets the access username
    public function setUsername($username)
    {
        $this->username = $username;
    }

    //returns the user name
    public function getUsername()
    {
        return $this->username;
    }

    //set the access password
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    //set the access port
    public function setPort($port)
    {
        $this->port = $port;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function __toString()
    {
        return "Host: ".$this->HostURL.", port: ".
        $this->port.", username: ".$this->username.
        ", password: ".$this->password.
        ", auth: ".var_export($this->SMTPAuth, true);
    }
}
