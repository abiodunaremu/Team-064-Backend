DROP TABLE IF EXISTS `broomy_db`.`FileGroup`;
CREATE TABLE `broomy_db`.`FileGroup`
(
vFileGroupID varchar(16) not null PRIMARY KEY,
vUserID varchar(16) not null
REFERENCES User(vUserID),   
iFileCreatorID int not null
REFERENCES FileCreator(iFileCreatorID),   
dCreateDate datetime not null,
dLastUpdate datetime not null,
cStatus char(1) not null
);

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnGenFileGroupID`$$
CREATE PROCEDURE `broomy_db`.`prnGenFileGroupID`(OUT FileGroupID varchar(16))
BEGIN
    DECLARE COUNTER INT;
    SELECT count(vFileGroupID) INTO COUNTER FROM FileGroup;
    SET COUNTER=COUNTER+1;
    SET FileGroupID=CONCAT('G', CAST(COUNTER AS CHAR(16)));
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnInsFileGroup`$$
CREATE PROCEDURE `broomy_db`.`prnInsFileGroup`(
vhSessionID varchar(16),
ihFileCreatorID int,
OUT vhFileGroupID varchar(16)
)
BEGIN
    IF EXISTS (SELECT * FROM CustomerSession 
            WHERE vCustomerSessionID = vhSessionID 
            AND dTimeOut IS NULL)
    THEN
        CALL prnGenFileGroupID(vhFileGroupID);
        SET @createdDate=now();
        INSERT INTO FileGroup
        VALUES(vhFileGroupID, vhSessionID, ihFileCreatorID
        , @createdDate, @createdDate, '0');
    ELSEIF EXISTS (SELECT * FROM AdminSession 
            WHERE vAdminSessionID = vhSessionID 
            AND dTimeOut IS NULL)
    THEN
        CALL prnGenFileGroupID(vhFileGroupID);
        SET @createdDate=now();
        INSERT INTO FileGroup
        VALUES(vhFileGroupID, vhSessionID, ihFileCreatorID
        , @createdDate, @createdDate, '0');
    END IF;
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnUpdFileGroup`$$
CREATE PROCEDURE `broomy_db`.`prnUpdFileGroup`(
vhFileGroupID varchar(16),
chStatus char(1),
OUT chOpStatus char(2)
)
BEGIN
    SELECT cStatus INTO @Status
    FROM FileGroup
    WHERE vFileGroupID=vhFileGroupID;

    SET @OrderDate=NOW();
    IF chStatus='' OR chStatus IS NULL
    THEN
        SET chStatus=@Status;
    END IF;
    UPDATE FileGroup
    SET cStatus=chStatus
    WHERE vFileGroupID=vhFileGroupID;

    SET chOpStatus = 'OK';
END$$
DELIMITER ;