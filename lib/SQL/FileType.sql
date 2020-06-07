USE broomy_db;

DROP TABLE IF EXISTS `broomy_db`.`FileType`;
CREATE TABLE `broomy_db`.`FileType`
(
iFileTypeID int auto_increment not null PRIMARY KEY,
vName varchar(50) not null unique,
tDescription text not null,
vExtension varchar(10) not null,
dCreateDate datetime not null,
dLastUpdate datetime not null,
cStatus char(1) not null
);
alter table `broomy_db`.`FileType` auto_increment = 1;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnInsFileType`$$
CREATE PROCEDURE `broomy_db`.`prnInsFileType`(
vhName varchar(50),
thDescription text,
vhExtension varchar(10)
)
BEGIN
    SET @createdDate=now();
    INSERT INTO FileType
    VALUES(default, vhName, thDescription
    , vhExtension, @createdDate, @createdDate, '0');
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnGenFileTypeCodeForName`$$
CREATE PROCEDURE `broomy_db`.`prnGenFileTypeCodeForName`(
    IN FileTypeName Varchar(10), OUT FileTypeID int)
    BEGIN
	IF EXISTS(SELECT * FROM FileType WHERE vName=FileTypeName)
	THEN
		SELECT iFileTypeID INTO FileTypeID 
		FROM FileType WHERE vName=FileTypeName;	
	ELSE
		SET @A='Nill';/**SELECT 'Specified Currency Name not be found';**/
	END IF;
    END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnUpdFileType`$$
CREATE PROCEDURE `broomy_db`.`prnUpdFileType`(
ihFileTypeID int,
vhName varchar(16),
thDescription text,
vhExtension varchar(10),
chStatus char(1)
)
BEGIN
    SELECT vName, cStatus, tDescription, vExtension
    INTO @Name, @Status, @Description, @Extension
    FROM FileType
    WHERE iFileTypeID=ihFileTypeID;

    SET @OrderDate=NOW();
    IF vhName='' OR vhName IS NULL
    THEN
        SET vhName=@Name;
    END IF;

    IF chStatus='' OR chStatus IS NULL
    THEN
        SET chStatus=@Status;
    END IF;

    IF thDescription='' OR thDescription IS NULL
    THEN
        SET thDescription=@Description;
    END IF;

    IF vhExtension='' OR vhExtension IS NULL
    THEN
        SET vhExtension = @Extension;
    END IF;

    UPDATE FileType
    SET vName=vhName, cStatus=chStatus
    , tDescription=thDescription
    , vExtension=vhExtension
    WHERE iFileTypeID=ihFileTypeID;
END$$
DELIMITER ;