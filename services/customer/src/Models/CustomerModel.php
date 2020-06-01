<?php
namespace Customer\Models;

/** Application level representation of the Customer Model */
class CustomerModel
{
    private $customerId;
    private $firstName;
    private $middleName;
    private $lastName;
    private $dateOfBirth;
    private $gender;
    private $nationality;
    private $phoneNumber;
    private $email;
    private $password;
    private $image;
    private $dateSignup;
    private $status;

    public function __construct(
        $customerId,
        $firstName,
        $middleName,
        $lastName,
        $dateOfBirth,
        $gender,
        $nationality,
        $phoneNumber,
        $email,
        $password,
        $image,
        $dateSignup,
        $status
    )
    {
        $this->customerId = $customerId;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->dateOfBirth = $dateOfBirth;
        $this->gender = $gender;
        $this->nationality = $nationality;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->password = $password;
        $this->image = $image;
        $this->dateSignup = $dateSignup;
        $this->status = $status;
    }

    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }
    
    public function getMiddleName()
    {
        return $this->middleName;
    }

    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }
    
    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
    
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }
 
    public function getGender()
    {
        return $this->gender;
    }
 
    public function getFullGender()
    {
        if ($this->gender === 'M') {
            return "Male";
        } elseif ($this->gender === 'F') {
            return "Female";
        }
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }
    
    public function getNationality()
    {
        return $this->nationality;
    }

    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }
    
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }
    
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
        
    public function getImage()
    {
        return $this->image ? $this->image : "";
    }

    public function setImage($image)
    {
        $this->image = $image;
    }
    
    public function getDateSignup()
    {
        return $this->dateSignup;
    }

    public function setDateSignup($dateSignup)
    {
        $this->dateSignup = $dateSignup;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}
