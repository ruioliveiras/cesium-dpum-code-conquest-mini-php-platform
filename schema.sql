START TRANSACTION;

DROP DATABASE IF EXISTS cesium_code ;
CREATE DATABASE cesium_code; 

use cesium_code;

DROP TABLE IF EXISTS code;
CREATE TABLE code (
	id INT not null AUTO_INCREMENT,
	dateCreate DATETIME not null, # date of creation
	points INT not null, # calculeted point in submition
	student VARCHAR(10), # student code 
	problem VARCHAR(30), # problem name 
	path VARCHAR(50),    # code path prefix name
	email VARCHAR(20), 
	PRIMARY KEY (id)
) ENGINE=INNODB;

# CREATE USER 'oliveiras'@'localhost' IDENTIFIED BY 'waterFall';
# GRANT ALL ON cesium_code.* TO 'oliveiras'@'localhost';

COMMIT;

# INSERT INTO code (dateCreate,points,student,problem,email) VALUES (NOW(),100,'a4544','tasco do Ze','gmailacom')