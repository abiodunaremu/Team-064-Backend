<?php
namespace Customer\Services;

use Customer\Repositories\CustomerRepository;
use Lib\EmailManager\EmailManager;
use Lib\EmailManager\SignupEmailContent;
use Lib\EmailManager\ResetPasswordEmailContent;
use Lib\FileManager\FileCreator\FileCreator;
use Lib\FileManager\FileCreator\FileCreatorHandler;
use Lib\FileManager\FileManager;
use Lib\FileManager\FileFormatter\FileFormatterFactory;
use Lib\FileUploadManager\FileUploadManager;

class CustomerService
{
    private $customerRepository;
    private $customer;

    public function __construct()
    {
        $this->customerRepository = new CustomerRepository();
    }

    public function registerCustomer(
        $firstName,
        $lastName,
        $phone,
        $email,
        $dob,
        $gender,
        $country,
        $deviceType,
        $region
    ) {
        //persist new customer into database
        $this->customer = $this->customerRepository
        ->registerCustomer(
            $firstName,
            $lastName,
            $phone,
            $email,
            $dob,
            $gender,
            $country,
            $deviceType,
            $region
        );
        
        if ($this->customer) {
            //send signup notification email to customer
            $emailManager = new EmailManager();
            $emailContent = new SignupEmailContent(
                $firstName." ".$lastName,
                $email,
                $this->customer->getPassword()
            );
            $resp =  $emailManager->setEmailContent($emailContent)->
            useDefaultSMTPEmailConnection()->
            usePHPMailerEmailAPI()->sendEmail();
        }
        
        return $this->customer;
    }
    
    public function getCustomerById($customerId)
    {
        return ($customerId) ?
        $this->customerRepository->getCustomerById($customerId) : null;
    }
    
    public function searchCustomersByCriteria($criteria)
    {
        return ($criteria) ?
        $this->customerRepository->searchCustomersByCriteria($criteria)
        : null;
    }
    
    public function resetCustomerPassword($email, $phoneNumber)
    {
        $this->customer = $this->customerRepository
        ->resetCustomerPassword($email, $phoneNumber);
        
        /**
        if ($this->customer) {
            //send reset notification email to customer
            $emailManager = new EmailManager();
            $emailContent = new ResetPasswordEmailContent(
                $this->customer->getFirstName()." ".$this->customer->getLastName(),
                $email,
                $this->customer->getPassword()
            );
            $resp =  $emailManager->setEmailContent($emailContent)->
            useDefaultSMTPEmailConnection()->
            usePHPMailerEmailAPI()->sendEmail();
        }
        */
        return $this->customer;
    }
    
    public function uploadProfilePicture($sessionId, $fileName, $FileExtension, $filePath, $fileSize)
    {
        $fileCreatorName = "CUSTOMER_PROFILE_PICTURE";
        $fileManager = new FileManager();
        $sourceFile = $fileManager
        ->createLogicFile($fileName, $FileExtension, $filePath, $fileSize);

        $fileFormatterFactory = new FileFormatterFactory();
        $fileCreatorManager = new FileCreatorHandler();
        $fileCreator = $fileCreatorManager->getFileCreatorByName($fileCreatorName);
        $JPEGImageFileFormatter = $fileFormatterFactory
        ->createJPEGImageFileFormatter($sourceFile, $fileCreator);

        $fileUploadManager = new FileUploadManager();
        $singleFileUploader = $fileUploadManager->createSingleFileUploader();
        return $singleFileUploader->upload($sessionId, $fileCreatorName, $JPEGImageFileFormatter);
    }
}
