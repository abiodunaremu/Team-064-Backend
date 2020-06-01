
DROP TABLE IF EXISTS `broomy_db`.`Country`;
CREATE TABLE `broomy_db`.`Country`
(
cCountryCode char(3) not null
PRIMARY KEY,
vName varchar(50) not null unique
);

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnGenCountryCode`$$
CREATE PROCEDURE `broomy_db`.`prnGenCountryCode`(OUT CountryCode char(3))
BEGIN
	DECLARE COUNTER INT;
	SELECT count(cCountryCode) into COUNTER FROM Country;
	SET COUNTER=COUNTER+1;
	SET CountryCode=CAST(COUNTER AS CHAR(3));
	CASE
		WHEN COUNTER>=0 and COUNTER<=9 THEN
		SET CountryCode=CONCAT('00',CAST(COUNTER AS CHAR(3)));
		WHEN COUNTER>9 and COUNTER<=99 THEN
		SET CountryCode=CONCAT('0',CAST(COUNTER AS CHAR(3)));
		WHEN COUNTER>99 and COUNTER<=999 THEN
		SET CountryCode=CAST(COUNTER AS CHAR(3));
	END CASE;
    END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnGenCountryCodeForName`$$
CREATE PROCEDURE `broomy_db`.`prnGenCountryCodeForName`(IN CountryName Varchar(50),OUT CountryCode Char(3) )
    BEGIN
	IF EXISTS(SELECT * FROM Country WHERE vName=CountryName)
	THEN
		SELECT cCountryCode INTO CountryCode FROM Country WHERE vName=CountryName;
	ELSE
		# SELECT 'Specified country not be found';
                SET CountryCode='';
	END IF;
    END$$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `broomy_db`.`prnInsCountry`$$
CREATE PROCEDURE `broomy_db`.`prnInsCountry`(IN CountryName Varchar(50))
BEGIN
	call prnGenCountryCode(@CountryCode);
	INSERT INTO Country
	VALUES(@CountryCode,CountryName);
END$$
DELIMITER ;