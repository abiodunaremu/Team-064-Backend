
DROP TABLE IF EXISTS `broomy_db`.`File`;
CREATE TABLE `broomy_db`.`File`
(
vFileID varchar(16) not null PRIMARY KEY,
vFileGroupID varchar(16) not null
REFERENCES FileGroup(vFileGroupID),
iFileTypeID int not null
REFERENCES FileType(iFileTypeID),
vOldName varchar(50) not null,
vExtension varchar(10) not null,
tPath text not null,
dSize decimal(10,2) not null,   
dCreateDate datetime not null,
dLastUpdate datetime not null,
cStatus char(1) not null
);

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnGenFileID`$$
CREATE PROCEDURE `broomy_db`.`prnGenFileID`(OUT FileID varchar(16))
BEGIN
    DECLARE COUNTER INT;
    SELECT count(vFileID) into COUNTER FROM File;
    SET COUNTER=COUNTER+1;
    SET FileID=CONCAT('D',CAST(COUNTER AS CHAR(16)));
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnGetFilePath`$$
CREATE PROCEDURE `broomy_db`.`prnGetFilePath`(FileID varchar(16),OUT FilePath varchar(100))
BEGIN
    SELECT vPath INTO FilePath FROM File WHERE vFileID=FileID;
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnInsFile`$$
CREATE PROCEDURE `broomy_db`.`prnInsFile`(
vhSessionID varchar(16),
vhFileGroupID varchar(16),
vhFileTypeName varchar(50),
vhOldName varchar(50),
thFileDirPath text,
vhExtension varchar(10),
dhSize decimal(10,2),
OUT vhFileID varchar(16)
)
BEGIN
    IF EXISTS (SELECT * FROM CustomerSession 
            WHERE vCustomerSessionID = vhSessionID 
            AND dTimeOut IS NULL)
    THEN
        CALL prnGenFileID(vhFileID);        
        CALL prnGenFileTypeCodeForName(vhFileTypeName, @FileTypeID);
        SET @OrderDate = NOW();
        INSERT INTO File
        VALUES(vhFileID, vhFileGroupID, @FileTypeID, vhOldName
        , vhExtension, thFileDirPath, dhSize, @OrderDate
        , @OrderDate, '0');
    ELSEIF EXISTS (SELECT * FROM AdminSession 
        WHERE vAdminSessionID = vhSessionID 
        AND dTimeOut IS NULL)
    THEN
        CALL prnGenFileID(vhFileID);        
        CALL prnGenFileTypeCodeForName(vhFileTypeName, @FileTypeID);
        SET @OrderDate = NOW();
        INSERT INTO File
        VALUES(vhFileID, vhFileGroupID, @FileTypeID, vhOldName
        , vhExtension, thFileDirPath, dhSize, @OrderDate
        , @OrderDate, '0');
    END IF;
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnUpdFile`$$
CREATE PROCEDURE `broomy_db`.`prnUpdFile`(
vhFileID varchar(16),
chStatus char(1)
)
BEGIN
    SELECT cStatus INTO @Status
    FROM File
    WHERE vFileID=vhFileID;

    SET @OrderDate=NOW();
    IF chStatus='' OR chStatus IS NULL
    THEN
        SET chStatus=@Status;
    END IF;
    
    UPDATE File
    SET cStatus=chStatus
    WHERE vFileID=vhFileID;
END$$
DELIMITER ;