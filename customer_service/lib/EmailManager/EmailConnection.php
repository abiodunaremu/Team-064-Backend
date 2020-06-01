<?php
namespace Lib\EmailManager;

interface EmailConnection
{
    public function setHostURL($URL);
    public function getHostURL();
    public function setUsername($userName);
    public function getUsername();
    public function setPassword($password);
    public function getPassword();
}
