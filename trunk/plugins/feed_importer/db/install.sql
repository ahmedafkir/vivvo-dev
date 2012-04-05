-- $Id: install.sql 5361 2010-05-20 15:03:29Z krcko $

/*@QUERY {"title":"Dropping table 'feeds'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/feeds;

/*@QUERY {"title":"Creating table 'feeds'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/feeds (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`category_id` INT NOT NULL,
	`feed` TEXT NOT NULL,
	`count` INT NOT NULL DEFAULT 0,
	`favicon` VARCHAR(250) NULL DEFAULT NULL,
	`author` VARCHAR(100) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `category_id` (`category_id`)
) ENGINE=InnoDB;

/*@QUERY {"title":"Adding new field to 'articles' table"}*/
ALTER TABLE /*{VIVVO_DB_PREFIX}*/articles
	ADD `feed_item_id` VARCHAR(32) NULL DEFAULT NULL,
	ADD INDEX (`feed_item_id`(32));