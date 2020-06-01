USE broomy_db;

DELIMITER $$
DROP VIEW IF EXISTS `broomy_db`.`vwCustomer`$$
CREATE VIEW `broomy_db`.`vwCustomer`(
vCustomerID,vFirstName,vMiddleName,vLastName,dDOB,dDOBDay,dDOBMonth,dDOBYear,
cGender,cCountryCode,vPhoneNumber,
vEmail,vPassword,vCountryName,vMediaGroupID, 
dDateCreated, cStatus
)
AS
SELECT vCustomerID,UPPER(vFirstName),UPPER(vMiddleName)
,UPPER(vLastName),DATE(dDOB),DAY(dDOB),MONTH(dDOB),YEAR(dDOB)
,cGender,Customer.cCountryCode,vPhone,
vEmail,vPassword,Country.vName,vMediaGroupID,
dRegDate, Customer.cStatus
FROM Customer JOIN Country
ON Customer.cCountryCode=Country.cCountryCode
$$
DELIMITER ;