<?php
namespace Customer\Models;

class CustomerSessionModel
{
    private $customerSessionId;
    private $customerId;
    private $timeIn;
    private $timeOut;
    private $timelastChecked;
    private $startState;
    private $checkState;
    private $endState;
    private $deviceType;
    private $region;

    public function __construct($customerSesssionId, $customerId, $timeIn, $timeOut, $timelastChecked, $startState, $checkState, $endState, $deviceType, $region)
    {
        $this->customerSessionId = $customerSesssionId;
        $this->customerId = $customerId;
        $this->timeIn = $timeIn;
        $this->timeOut = $timeOut;
        $this->timelastChecked = $timelastChecked;
        $this->startState = $startState;
        $this->checkState = $checkState;
        $this->endState = $endState;
        $this->deviceType = $deviceType;
        $this->region = $region;
    }

    public function getCustomerSessionId()
    {
        return $this->customerSessionId;
    }

    public function setCustomerSessionId($customerSessionId)
    {
        $this->customerSessionId = $customerSessionId;
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function setCustomerId($customerSessionId)
    {
        $this->customerSessionId = $customerSessionId;
    }

    public function getTimeIn()
    {
        return $this->timeIn;
    }

    public function setTimeIn($timeIn)
    {
        $this->timeIn = $timeIn;
    }

    public function getTimeOut()
    {
        return $this->timeOut;
    }

    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;
    }

    public function getTimeLastChecked()
    {
        return $this->timelastChecked;
    }

    public function setTimeLastChecked($timelastChecked)
    {
        $this->timelastChecked = $timelastChecked;
    }

    public function getStartState()
    {
        return $this->startState;
    }

    public function setStartState($startState)
    {
        $this->startState = $startState;
    }

    public function getCheckState()
    {
        return $this->checkState;
    }

    public function setCheckState($checkState)
    {
        $this->checkState = $checkState;
    }

    public function getEndState()
    {
        return $this->endState;
    }

    public function setEndState($endState)
    {
        $this->endState = $endState;
    }

    public function getDeviceType()
    {
        return $this->deviceType;
    }

    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }
}
