-- $Id: install.sql 5361 2010-05-20 15:03:29Z krcko $

/*@QUERY {"title":"Dropping table 'poll_questions'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/poll_questions;

/*@QUERY {"title":"Creating table 'poll_questions'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/poll_questions (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`question` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`start_date` DATETIME NOT NULL,
	`end_date` DATETIME NOT NULL,
	`status` INT NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `status` (`status`),
	KEY `start_date` (`start_date`)
) ENGINE = InnoDB;

/*@QUERY {"title":"Installing demo poll"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/poll_questions VALUES
(1, 'Like Our New Look?', 'Do you like our new Vivvo look & feel?', '/*{VIVVO_POLL_START_DATE}*/', '/*{VIVVO_POLL_END_DATE}*/', '1');

/*@QUERY {"title":"Dropping table 'poll_answers'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/poll_answers;

/*@QUERY {"title":"Creating table 'poll_answers'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/poll_answers (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`poll_id` INT UNSIGNED NOT NULL,
	`answer` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`vote` INT NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `poll_id` (`poll_id`)
) ENGINE = InnoDB;

/*@QUERY {"title":"Adding answers to demo poll"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/poll_answers VALUES
(1, 1, 'It\'s great', 1),
(2, 1, 'Looks OK', 1),
(3, 1, 'Nothing special', 1),
(4, 1, 'Whatever', 1),
(5, 1, 'It\'s horrible', 1);
