
USE broomy_db;

DROP TABLE IF EXISTS `broomy_db`.`CustomerSession`;
CREATE TABLE `broomy_db`.`CustomerSession`
(
vCustomerSessionID varchar(16) not null PRIMARY KEY,
vCustomerID varchar(16) not null,
dTimeIN datetime not null,
cStartState char(1) not null,
dTimeLastCheck datetime,
cCheckState char(1),
dTimeOUT datetime,
cEndState char(1),
cDeviceType char(1) not null,
vRegion varchar(32),
UNIQUE(vCustomerID,dTimeIN)
);

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnGenCustomerSessionID`$$
CREATE PROCEDURE `broomy_db`.`prnGenCustomerSessionID`(OUT CustomerSessionID varchar(16))
BEGIN
    DECLARE COUNTER INT;
    SELECT count(vCustomerSessionID) into COUNTER FROM CustomerSession;
    SET COUNTER=COUNTER+1;
    SET CustomerSessionID=CONCAT('S',CAST(COUNTER AS CHAR(16)));
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnSessionLogin`$$
CREATE PROCEDURE `broomy_db`.`prnSessionLogin`(
vhCustomername varchar(50),
vhPassword varchar(50),
chStartState char(1),
chDeviceType char(1),
vhRegion varchar(32),
OUT vhCustomerID varchar(16),
OUT vhCustomerSessionID varchar(16),
OUT vhTimeIn datetime,
OUT vhTimeOut datetime,
OUT vhTimeLastCheck datetime,
OUT chCheckState char(1),
OUT chEndState char(1)
)
BEGIN
    IF EXISTS (SELECT * FROM Customer 
    WHERE (vEmail=vhCustomername or vPhone=vhCustomername) 
    AND BINARY vPassword=sha2(vhPassword, 256))
    THEN
        SELECT vCustomerID INTO vhCustomerID FROM Customer 
        WHERE (vEmail=vhCustomername or vPhone=vhCustomername) 
        AND BINARY vPassword=sha2(vhPassword,256);
        CALL prnInsCustomerSession(vhCustomerID,chStartState,
        chDeviceType,vhRegion,vhCustomerSessionID);
        SET vhTimeIn = NOW();
        SET vhTimeOut = NULL;
        SET vhTimeLastCheck = NOW();
        SET chCheckState = chStartState;
        SET chEndState = NULL;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnInsCustomerSession`$$
CREATE PROCEDURE `broomy_db`.`prnInsCustomerSession`(
vhCustomerID varchar(16),
chStartState char(1),
chDeviceType char(1),
vhRegion varchar(32),
OUT vhCustomerSessionID varchar(16)
)
BEGIN
    CALL prnGenCustomerSessionID(vhCustomerSessionID);
    SET @OrderDate=now();
    UPDATE CustomerSession
    SET cEndState='0',dTimeOut=@OrderDate
    WHERE vCustomerID=vhCustomerID AND dTimeOut IS NULL AND cDeviceType=chDeviceType;
    INSERT INTO CustomerSession
    VALUES(vhCustomerSessionID,vhCustomerID,@OrderDate,chStartState,null,null,null,null,
    chDeviceType,vhRegion);
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnUpdCustomerSession`$$
CREATE PROCEDURE `broomy_db`.`prnUpdCustomerSession`(
vhCustomerSessionID varchar(16),
chEndState char(1),
OUT vhCustomerID varchar(16)
)
BEGIN
    SELECT vCustomerID,cEndState,dTimeOUT INTO vhCustomerID,@EndState,@TimeOut
    FROM CustomerSession
    WHERE (vCustomerSessionID=vhCustomerSessionID) AND dTimeOut IS NULL LIMIT 1;

    SET @OrderDate=NOW();
    IF chEndState='' OR chEndState IS NULL
    THEN
        SET chEndState=@EndState;
        SET @OrderDate=@TimeOut;
    END IF;
    UPDATE CustomerSession
    SET cEndState=chEndState,dTimeOut=@OrderDate
    WHERE (vCustomerSessionID=vhCustomerSessionID) AND dTimeOUT is NULL;
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnVerifyActiveCustomerSession`$$
CREATE PROCEDURE `broomy_db`.`prnVerifyActiveCustomerSession`(
vhCustomerSessionID varchar(16),
OUT vhCustomerID varchar(16)
)
BEGIN
    SELECT vCustomerID,cEndState INTO vhCustomerID,@EndState
    FROM CustomerSession
    WHERE (vCustomerSessionID=vhCustomerSessionID) AND dTimeOut IS NULL LIMIT 1;
    SET @OrderDate=NOW();
    IF (vhCustomerID!='' AND vhCustomerID IS NOT NULL)
    THEN
        UPDATE CustomerSession
        SET cCheckState='0',dTimeLastCheck=@OrderDate
        WHERE vCustomerSessionID=vhCustomerSessionID;
    END IF;
END$$
DELIMITER ;
