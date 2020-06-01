
DROP TABLE IF EXISTS `broomy_db`.`Customer`;
CREATE TABLE `broomy_db`.`Customer`
(
vCustomerID varchar(16) not null
PRIMARY KEY ,
vFirstName varchar(40) not null,
vMiddleName varchar(40) not null,
vLastName varchar(40) not null,
cGender char(1) not null,
tAddress text,
vCity varchar(20),
vState varchar(20),
cCountryCode Char(3)
REFERENCES Country(cCountryCode),
vZip varchar(8),
dDOB datetime,
vEmail varchar(40) unique not null,
vPhone varchar(40) unique,
vPassword varchar(256),
vMediaGroupID varchar(16)
REFERENCES FileGroup(vFileGroupID),
dRegDate datetime not null,
dLastUpdate datetime not null,
cStatus char(1)
);

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnGenCustomerID`$$
CREATE PROCEDURE `broomy_db`.`prnGenCustomerID`(OUT CustomerID varchar(16))
BEGIN
    DECLARE COUNTER INT;
    SELECT count(vCustomerID) into COUNTER FROM Customer;
    SET COUNTER=COUNTER+1;
    SET CustomerID=CONCAT('C',CAST(COUNTER AS CHAR(16)));
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnGenCustomerCodeForEmail`$$
CREATE PROCEDURE `broomy_db`.`prnGenCustomerCodeForEmail`(IN Email Varchar(40),OUT CustomerCode varChar(16))
    BEGIN
	IF EXISTS(SELECT * FROM Customer WHERE vEmail=Email)
	THEN
		SELECT vCustomerID INTO CustomerCode 
		FROM Customer WHERE vEmail=Email;	
	ELSE
		SET @A='Nill';/**SELECT 'Specified Customer Name not be found';**/
	END IF;
    END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnInsCustomer`$$
CREATE PROCEDURE `broomy_db`.`prnInsCustomer`(
vhFirstName varchar(40),
vhLastName varchar(40),
vhPhone varchar(40),
vhEmail varchar(40),
vhPassword varchar(256),
vhDOB datetime,
chGender Char(1),
vhCountryName varchar(50),
OUT vhCustomerID varchar(16)
)
BEGIN
    CALL prnGenCustomerID(vhCustomerID);
    CALL prnGenCountryCodeForName(vhCountryName,@CountryCode);
    SET @OrderDate=now();
    INSERT INTO Customer
    VALUES(vhCustomerID,vhFirstName,'',vhLastName,
    chGender,'','','',
    @CountryCode,'',vhDOB,vhEmail,vhPhone,vhPassword,
    NULL, @OrderDate,@OrderDate,'0');
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnSignUpCustomer`$$
CREATE PROCEDURE `broomy_db`.`prnSignUpCustomer`(
vhFirstName varchar(20),
vhLastName varchar(20),
vhPhoneNumber varchar(16),
vhEmail varchar(50),
vhPassword varchar(15),
dhDOB varchar(15),
chGender char(1),
chNationality varchar(50),
chDeviceType char(1),
vhRegion char(32),
OUT vhSessionID varchar(16)
)
BEGIN        
        CALL prnInsCustomer(vhFirstName,vhLastName,vhPhoneNumber,vhEmail,
        sha2(vhPassword,256),dhDOB,chGender,chNationality,@customerID);
        SET @Username = vhPhoneNumber;
        IF(vhEmail != '' AND vhEmail IS NOT NULL)
        THEN
            SET @Username = vhEmail;
        END IF;
        CALL prnSessionLogin(@Username,vhPassword,'n',
        chDeviceType,vhRegion,@customerID,vhSessionID, @timeIn, 
        @timeOut, @timeLastCheck, @checkState, @endState);        
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnChangeCustomerPassword`$$
CREATE PROCEDURE `broomy_db`.`prnChangeCustomerPassword`(
vhCustomerID varchar(16),
vhPassword varchar(15),
OUT vhCustomerIDo varchar(16)
)
BEGIN
	IF EXISTS (SELECT * FROM Customer WHERE vCustomerID=vhCustomerID)
	THEN
		UPDATE Customer
		SET vPassword=sha2(vhPassword,256)
		WHERE vCustomerID=vhCustomerID;
		SET vhCustomerIDo=vhCustomerID;
	END IF;
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnUpdCustomerPassword`$$
CREATE PROCEDURE `broomy_db`.`prnUpdCustomerPassword`(
vhEmail varchar(50),
vhPhonenumber varchar(50),
vhPassword varchar(15),
OUT vhCustomerID varchar(16)
)
BEGIN
    SELECT vPhone,vEmail,vPassword,vCustomerID
    INTO @PhoneNumber,@Email,@Password,vhCustomerID
    FROM Customer
    WHERE vPhone=vhPhoneNumber AND vEmail=vhEmail;

    IF vhPassword='' OR vhPassword IS NULL
    THEN
        SET vhPassword=@Password;
	ELSE
		SET vhPassword=sha2(vhPassword,256);
    END IF;

    SET @OrderDate=now();
    UPDATE Customer
    SET vPassword=vhPassword
    WHERE vCustomerID=vhCustomerID;
END$$
DELIMITER ;
