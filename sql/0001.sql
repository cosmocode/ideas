SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `ideas`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user` (
  `login` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(45) NULL ,
  `magic` VARCHAR(40) NULL ,
  PRIMARY KEY (`login`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `ideas`.`idea`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `idea` (
  `id` INT NOT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `description` TEXT NOT NULL ,
  `created` DATETIME NULL ,
  `login` VARCHAR(255) NOT NULL ,
  `status` VARCHAR(45) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_idea_user_idx` (`login` ASC) ,
  FULLTEXT INDEX `ft_idx` (`title` ASC, `description` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `ideas`.`vote`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `vote` (
  `idea` INT NOT NULL ,
  `login` VARCHAR(255) NOT NULL ,
  `vote` INT NULL ,
  `voted` TIMESTAMP NULL ,
  PRIMARY KEY (`idea`, `login`) ,
  INDEX `fk_vote_idea1_idx` (`idea` ASC) ,
  INDEX `fk_vote_user1_idx` (`login` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
