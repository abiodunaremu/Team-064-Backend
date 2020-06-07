
DELIMITER $$
DROP VIEW IF EXISTS `broomy_db`.`vwFile`$$
CREATE VIEW `broomy_db`.`vwFile`(
vFileID ,
vFileGroupID ,
iFileTypeID , vFileTypeName,
vOldName ,
vExtension ,
tPath ,
dSize ,
dCreateDate ,
dLastUpdate,cStatus
)
AS
SELECT 
File.vFileID ,
File.vFileGroupID ,
File.iFileTypeID , FileType.vName,
File.vOldName ,
File.vExtension ,
File.tPath ,
File.dSize ,
File.dCreateDate ,
File.dLastUpdate, File.cStatus
FROM File JOIN FileType
ON File.iFileTypeID = FileType.iFileTypeID
$$
DELIMITER ;
