USE broomy_db;

DROP TABLE IF EXISTS `broomy_db`.`FileCreator`;
CREATE TABLE `broomy_db`.`FileCreator`
(
iFileCreatorID int auto_increment not null PRIMARY KEY,
vName varchar(50) not null unique,
tDescription text not null,   
tFileDestinationPath text not null,
dCreateDate datetime not null,
dLastUpdate datetime not null,
cStatus char(1) not null
);
alter table `broomy_db`.`FileCreator` auto_increment=1;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnGenFileCreatorCodeForName`$$
CREATE PROCEDURE `broomy_db`.`prnGenFileCreatorCodeForName`(
    IN FileCreatorName Varchar(10), OUT FileCreatorID int)
    BEGIN
	IF EXISTS(SELECT * FROM FileCreator WHERE iFileCreatorID=FileCreatorCode)
	THEN
		SELECT iFileCreatorID INTO FileCreatorID 
		FROM FileCreator WHERE vName=FileCreatorName;	
	ELSE
		SET @A='Nill';/**SELECT 'Specified Currency Name not be found';**/
	END IF;
    END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnInsFileCreator`$$
CREATE PROCEDURE `broomy_db`.`prnInsFileCreator`(
vhName varchar(50),
thDescription text,
thFileDestinationPath text
)
BEGIN
    SET @createdDate=now();
    INSERT INTO FileCreator
    VALUES(default, vhName, thDescription, thFileDestinationPath, @createdDate, @createdDate, '0');
END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnUpdFileCreator`$$
CREATE PROCEDURE `broomy_db`.`prnUpdFileCreator`(
ihFileCreatorID int,
vhName varchar(16),
thDescription text,
thFileDestinationPath text,
chStatus char(1)
)
BEGIN
    SELECT vName, cStatus, tDescription, tFileDestinationPath 
    INTO @Name, @Status, @Description, @FileDestinationPath
    FROM FileCreator
    WHERE iFileCreatorID=ihFileCreatorID;

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

    IF thFileDestinationPath='' OR thFileDestinationPath IS NULL
    THEN
        SET thFileDestinationPath=@FileDestinationPath;
    END IF;

    UPDATE FileCreator
    SET vName=vhName, cStatus=chStatus
    , tDescription=thDescription, tFileDestinationPath=thFileDestinationPath
    WHERE iFileCreatorID=ihFileCreatorID;
END$$
DELIMITER ;