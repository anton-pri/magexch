DROP FUNCTION IF EXISTS alphanum; 
DELIMITER | 
CREATE FUNCTION alphanum( str CHAR(32) ) RETURNS CHAR(16) 
BEGIN 
  DECLARE i, len SMALLINT DEFAULT 1; 
  DECLARE ret CHAR(32) DEFAULT ''; 
  DECLARE c CHAR(1); 
  SET len = CHAR_LENGTH( str ); 
  REPEAT 
    BEGIN 
      SET c = MID( str, i, 1 ); 
      IF '0123456789nvNV' LIKE CONCAT('%',c,'%') THEN 
        SET ret=CONCAT(ret,c); 
      END IF; 
      SET i = i + 1; 
    END; 
  UNTIL i > len END REPEAT; 
  RETURN ret; 
END | 
DELIMITER ; 
