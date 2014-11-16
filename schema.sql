START TRANSACTION;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `dpum` ;
CREATE SCHEMA IF NOT EXISTS `dpum` ;
USE `dpum` ;

-- -----------------------------------------------------
-- Table `dpum`.`problem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `dpum`.`problem` ;

CREATE TABLE IF NOT EXISTS `dpum`.`problem` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(75) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `end_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `dpum`.`submission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `dpum`.`submission` ;

CREATE TABLE IF NOT EXISTS `dpum`.`submission` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `problem_id` INT NOT NULL,
  `student_code` VARCHAR(10) NULL DEFAULT NULL,
  `student_name` VARCHAR(20) NULL DEFAULT NULL,
  `student_email` VARCHAR(30) NULL DEFAULT NULL,
  `dateCreate` DATETIME NOT NULL,
  `points` INT NOT NULL,
  `path` VARCHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_1518c45e-6dbb-11e4-b4f9-7c0507c6a780` (`problem_id` ASC),
  CONSTRAINT `fk_1518c45e-6dbb-11e4-b4f9-7c0507c6a780`
    FOREIGN KEY (`problem_id`)
    REFERENCES `dpum`.`problem` (`id`))
ENGINE = InnoDB;

USE `dpum` ;

-- -----------------------------------------------------
-- Placeholder table for view `dpum`.`guest_submission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dpum`.`guest_submission` (`student_name` INT, `points` INT, `problem` INT);

-- -----------------------------------------------------
-- Placeholder table for view `dpum`.`guest_problem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dpum`.`guest_problem` (`id` INT, `name` INT, `description` INT, `end_date` INT);

-- -----------------------------------------------------
-- Placeholder table for view `dpum`.`admin_submission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dpum`.`admin_submission` (`id` INT, `problem_id` INT, `student_code` INT, `student_name` INT, `student_email` INT, `dateCreate` INT, `points` INT, `path` INT, `problem` INT);

-- -----------------------------------------------------
-- Placeholder table for view `dpum`.`admin_problem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dpum`.`admin_problem` (`id` INT, `name` INT, `description` INT, `end_date` INT);

-- -----------------------------------------------------
-- View `dpum`.`guest_submission`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `dpum`.`guest_submission` ;
DROP TABLE IF EXISTS `dpum`.`guest_submission`;
USE `dpum`;
CREATE  OR REPLACE VIEW `guest_submission` AS
    select 
        s.`student_name` as `student_name`,
        s.`points` as points,
        p.`name` as problem
    from
        submission s
            inner join
        problem p ON p.id = s.problem_id;

-- -----------------------------------------------------
-- View `dpum`.`guest_problem`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `dpum`.`guest_problem` ;
DROP TABLE IF EXISTS `dpum`.`guest_problem`;
USE `dpum`;
CREATE  OR REPLACE VIEW `guest_problem` AS
    select 
        id, name, description, end_date
    from
        problem;


-- -----------------------------------------------------
-- View `dpum`.`admin_submission`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `dpum`.`admin_submission` ;
DROP TABLE IF EXISTS `dpum`.`admin_submission`;
USE `dpum`;
CREATE  OR REPLACE VIEW `admin_submission` AS
    select 
        s.*,
        p.`name` as problem
    from
        submission s
            inner join
        problem p ON p.id = s.problem_id;

-- -----------------------------------------------------
-- View `dpum`.`admin_problem`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `dpum`.`admin_problem` ;
DROP TABLE IF EXISTS `dpum`.`admin_problem`;
USE `dpum`;
CREATE  OR REPLACE VIEW `admin_problem` AS
    select 
        *
    from
        problem;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `dpum`.`problem`
-- -----------------------------------------------------
START TRANSACTION;
USE `dpum`;
INSERT INTO `dpum`.`problem` (`id`, `name`, `description`, `end_date`) VALUES (1, 'test1', 'O primeiro teste a aplicacao', ' 2014-11-16 18:30:36');

COMMIT;


-- -----------------------------------------------------
-- Data for table `dpum`.`submission`
-- -----------------------------------------------------
START TRANSACTION;
USE `dpum`;
INSERT INTO `dpum`.`submission` (`id`, `problem_id`, `student_code`, `student_name`, `student_email`, `dateCreate`, `points`, `path`) VALUES (1, 1, 'a67661', 'rui oliveira', 'rui96pedro@hotmail.com', '2014-11-16 18:43:01', 10, NULL);

COMMIT;



COMMIT;

# INSERT INTO code (dateCreate,points,student,problem,email) VALUES (NOW(),100,'a4544','tasco do Ze','gmailacom')
# CREATE USER 'oliveiras'@'localhost' IDENTIFIED BY 'waterFall';
# GRANT ALL ON dpum.* TO 'oliveiras'@'localhost';
