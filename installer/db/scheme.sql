-- $Id: scheme.sql 5390 2010-05-25 13:06:09Z krcko $

/*@QUERY {"title":"Altering database to UTF8"}*/
ALTER DATABASE `/*{VIVVO_DB_DATABASE}*/` CHARACTER SET utf8 COLLATE utf8_unicode_ci;


/*@QUERY {"title":"Dropping table 'articles'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/articles;
/*@QUERY {"title":"Creating table 'articles'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/articles (
	`id` INT NOT NULL AUTO_INCREMENT,
	`category_id` INT(11) NOT NULL,
	`user_id` INT(11) DEFAULT NULL,
	`user_domain` VARCHAR(255) DEFAULT 'vivvo@localhost',
	`author` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`title` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
	`image` VARCHAR(255) DEFAULT NULL,
	`created` DATETIME NOT NULL,
	`last_edited` DATETIME NOT NULL,
	`body` TEXT COLLATE utf8_unicode_ci NULL,
	`last_read` DATETIME DEFAULT NULL,
	`times_read` INT(6) DEFAULT 0,
	`today_read` INT(6) DEFAULT 0,
	`status` INT(3) NOT NULL DEFAULT 0,
	`sefriendly` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`link` VARCHAR(255) DEFAULT NULL,
	`order_num` INT(6) DEFAULT NULL,
	`show_poll` ENUM('0','1') DEFAULT '1',
	`show_comment` ENUM('0','1') DEFAULT '1',
	`rss_feed` ENUM('0','1') DEFAULT '1',
	`keywords` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`description` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`emailed` INT(11) DEFAULT 0,
	`vote_num` INT(11) DEFAULT 0,
	`vote_sum` INT(11) DEFAULT 0,
	`abstract` TEXT COLLATE utf8_unicode_ci,
	`image_caption` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `status_created_category_id` (`status`,`created`,`category_id`),
	KEY `status_created` (`status`,`created`),
	KEY `status_created_user_id` (`status`,`created`,`user_id`),
	KEY `sefriendly_category_id` (`sefriendly`,`category_id`),
	KEY `status_created_emailed` (`status`,`created`,`emailed`),
	KEY `status_created_today_read` (`status`,`created`,`today_read`),
	KEY `status_created_times_read` (`status`,`created`,`times_read`),
	KEY `order_num` (`order_num`),
	FULLTEXT KEY `title_body` (`title`,`body`,`abstract`),
	FULLTEXT KEY `title_abstract` (`title`,`abstract`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci, AUTO_INCREMENT = 3000;

/*@QUERY {"title":"Dropping table 'articles_revisions'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/articles_revisions;
/*@QUERY {"title":"Creating table 'articles_revisions'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/articles_revisions (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `version` INT DEFAULT 1,
  `title` VARCHAR(255) DEFAULT NULL,
  `body` TEXT,
  `abstract` TEXT,
  `creator_id` INT UNSIGNED DEFAULT NULL,
  `created_time` TIMESTAMP NULL DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `version` (`version`),
  KEY `article_id_version` (`article_id`,`version`),
  KEY `article_id` (`article_id`),
  KEY `type` (`type`),
  KEY `article_id_type` (`article_id`,`type`),
  KEY `article_id_version_type` (`article_id`,`version`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*@QUERY {"title":"Dropping table 'articles_stats'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/articles_stats;
/*@QUERY {"title":"Creating table 'articles_stats'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/articles_stats (
	`article_id` INT(11) NOT NULL AUTO_INCREMENT,
	`last_read` DATETIME DEFAULT NULL,
	`times_read` INT(11) DEFAULT 0,
	`today_read` INT(11) DEFAULT 0,
	`updated` TINYINT(4) DEFAULT 0,
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`article_id`),
	KEY `updated` (`updated`),
	KEY `created` (`created`,`updated`)
) ENGINE=InnoDB;

/*@QUERY {"title":"Dropping table 'articles_tags'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/articles_tags;
/*@QUERY {"title":"Creating table 'articles_tags'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/articles_tags (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`tag_id` INT(11) NOT NULL,
	`article_id` INT(11) NOT NULL,
	`tags_group_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	`user_id` INT(11) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `article_id` (`article_id`),
	KEY `user_id` (`user_id`),
	KEY `tag_id` (`tag_id`),
	KEY `tags_group_id` (`tags_group_id`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'articles_schedule'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/articles_schedule;
/*@QUERY {"title":"Creating table 'articles_schedule'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/articles_schedule (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`article_id` INT(10) UNSIGNED NOT NULL,
	`minute` BIGINT(20) UNSIGNED NOT NULL default '0',
	`hour` BIGINT(20) UNSIGNED NOT NULL default '0',
	`dom` BIGINT(20) UNSIGNED NOT NULL default '0',
	`month` BIGINT(20) UNSIGNED NOT NULL default '0',
	`dow` BIGINT(20) UNSIGNED NOT NULL default '0',
	`year` SMALLINT(5) UNSIGNED DEFAULT NULL,
	`duration` INT(10) UNSIGNED DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `article_id` (`article_id`),
	KEY `time` (`minute`,`hour`,`dom`,`month`,`dow`),
	KEY `year` (`year`),
	KEY `duration` (`duration`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'categories'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/categories;
/*@QUERY {"title":"Creating table 'categories'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/categories (
	`id` INT(6) NOT NULL AUTO_INCREMENT,
	`category_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`parent_cat` INT(4) DEFAULT 0,
	`order_num` INT(4) DEFAULT 0,
	`article_num` INT(4) DEFAULT 0,
	`template` VARCHAR(255) DEFAULT NULL,
	`css` VARCHAR(255) DEFAULT NULL,
	`view_subcat` INT(1) DEFAULT 1,
	`redirect` VARCHAR(255) DEFAULT NULL,
	`image` VARCHAR(255) DEFAULT NULL,
	`sefriendly` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	`article_template` VARCHAR(255) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `order_num` (`order_num`)
) ENGINE=MyISAM, DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*@QUERY {"title":"Dropping table 'comments'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/comments;
/*@QUERY {"title":"Creating table 'comments'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/comments (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`article_id` INT(11) NULL DEFAULT NULL,
	`user_id` INT(11) NULL DEFAULT NULL,
	`description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
	`description_src` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
	`create_dt` DATETIME DEFAULT NULL,
	`author` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
	`email` VARCHAR(255) NULL DEFAULT NULL,
	`ip` VARCHAR(20) NOT NULL default '',
	`status` ENUM('0','1','2') NOT NULL DEFAULT '0',
	`www` VARCHAR(255) NULL DEFAULT NULL,
	`vote` INT(11) DEFAULT 0,
	`reply_to` INT(11) NULL DEFAULT NULL,
	`root_comment` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `article_id` (`article_id`),
	KEY `create_dt` (`create_dt`),
	KEY `user_id` (`user_id`),
	KEY `reply_to` (`reply_to`),
	KEY `root_comment` (`root_comment`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'configuration'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/configuration;
/*@QUERY {"title":"Creating table 'configuration'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/configuration (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`variable_name` VARCHAR(255) NOT NULL DEFAULT '',
	`variable_property` VARCHAR(255) DEFAULT NULL,
	`variable_value` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
	`module` VARCHAR(50) DEFAULT NULL,
	`domain_id` INT(10) UNSIGNED DEFAULT NULL,
	`reg_exp` VARCHAR(255) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `module` (`module`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'cron'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/cron;
/*@QUERY {"title":"Creating table 'cron'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/cron (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`lastrun` INT(10) NOT NULL,
	`nextrun` INT(10) DEFAULT NULL,
	`time_mask` VARCHAR(32) NOT NULL,
	`file` VARCHAR(255) NOT NULL,
	`class` VARCHAR(255) default NULL,
	`method` VARCHAR(255) NOT NULL,
	`arguments` VARCHAR(255) NOT NULL,
	`hash` VARCHAR(32) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `hash` (`hash`),
	KEY `nextrun` (`nextrun`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'cron'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/tags;
/*@QUERY {"title":"Creating table 'tags'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/tags (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`sefriendly` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	PRIMARY KEY (`id`),
	KEY `name` (`name`),
	KEY `sefriendly` (`sefriendly`)
) ENGINE=MyISAM, AUTO_INCREMENT=101;

/*@QUERY {"title":"Dropping table 'tags_groups'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/tags_groups;
/*@QUERY {"title":"Creating table 'tags_groups'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/tags_groups (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL default '',
	`url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`template` VARCHAR(100) NOT NULL default 'default.tpl',
	`tag_template` VARCHAR(100) NOT NULL default 'default.tpl',
	`metadata` TEXT,
	PRIMARY KEY (`id`),
	KEY `url` (`url`)
) ENGINE=MyISAM, AUTO_INCREMENT=101;

/*@QUERY {"title":"Dropping table 'tags_to_tags_groups'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/tags_to_tags_groups;
/*@QUERY {"title":"Creating table 'tags_to_tags_groups'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/tags_to_tags_groups (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`tags_group_id` INT(10) UNSIGNED NOT NULL default '0',
	`tag_id` INT(10) UNSIGNED NOT NULL default '0',
	PRIMARY KEY (`id`),
	KEY `tags_group_id` (`tags_group_id`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'user_filters'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/user_filters;
/*@QUERY {"title":"Creating table 'user_filters'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/user_filters (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL,
	`query` TEXT,
	`section` VARCHAR(45) NOT NULL,
	`name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`id`),
	KEY `user_id_section` (`user_id`,`section`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'users'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/users;
/*@QUERY {"title":"Creating table 'users'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/users (
	`userid` int(9) NOT NULL AUTO_INCREMENT,
	`first_name` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
	`last_name` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
	`email_address` VARCHAR(255) NOT NULL DEFAULT '',
	`username` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`password` VARCHAR(255) NOT NULL DEFAULT '',
	`activated` ENUM('-1','0','1','2') NOT NULL DEFAULT '0',
	`picture` VARCHAR(100) DEFAULT NULL,
	`bio` TEXT CHARACTER SET UTF8 COLLATE utf8_unicode_ci,
	`www` VARCHAR(100) DEFAULT NULL,
	`logins` INT(9) NOT NULL DEFAULT '0',
	`last_login` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`session_id` VARCHAR(32) DEFAULT NULL,
	`created` DATETIME NOT NULL,
	`user_privileges` TEXT NULL DEFAULT NULL,
	PRIMARY KEY (`userid`),
	KEY `username` (`username`),
	KEY `username_activated` (`username`,`activated`),
	KEY `userid_activated` (`userid`,`activated`),
	KEY `session_id` (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*@QUERY {"title":"Dropping table 'mail_queue'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/mail_queue;
/*@QUERY {"title":"Creating table 'mail_queue'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/mail_queue (
	`id` bigint(20) NOT NULL default '0',
	`create_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`time_to_send` datetime NOT NULL default '0000-00-00 00:00:00',
	`sent_time` datetime default NULL,
	`id_user` bigint(20) NOT NULL default '0',
	`ip` varchar(20) collate utf8_unicode_ci NOT NULL default 'unknown',
	`sender` varchar(50) collate utf8_unicode_ci NOT NULL default '',
	`recipient` text collate utf8_unicode_ci NOT NULL,
	`headers` text collate utf8_unicode_ci NOT NULL,
	`body` longtext collate utf8_unicode_ci NOT NULL,
	`try_sent` tinyint(4) NOT NULL default '0',
	`delete_after_send` tinyint(1) NOT NULL default '1',
	PRIMARY KEY (`id`),
	KEY `id` (`id`),
	KEY `time_to_send` (`time_to_send`),
	KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*@QUERY {"title":"Dropping table 'mail_queue_seq'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/mail_queue_seq;
/*@QUERY {"title":"Creating table 'mail_queue_seq'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/mail_queue_seq (
	`sequence` int(11) NOT NULL auto_increment,
	PRIMARY KEY (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*@QUERY {"title":"Dropping table 'group'"}*/
DROP TABLE IF EXISTS `/*{VIVVO_DB_PREFIX}*/group`;
/*@QUERY {"title":"Creating table 'group'"}*/
CREATE TABLE `/*{VIVVO_DB_PREFIX}*/group` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
	`domain_id` INT(10) UNSIGNED DEFAULT NULL,
	`allow_delete` INT(2) DEFAULT '1',
	`allow_edit` INT(2) DEFAULT '1',
	PRIMARY KEY (`id`),
	KEY `name` (`name`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'group_privileges'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/group_privileges;
/*@QUERY {"title":"Creating table 'group_privileges'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/group_privileges (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`group_id` INT(10) UNSIGNED NOT NULL,
	`user_source` VARCHAR(50) NOT NULL DEFAULT 'vivvo@localhost',
	`privileges` TEXT,
	PRIMARY KEY (`id`),
	KEY `group_source` (`group_id`,`user_source`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'group_user'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/group_user;
/*@QUERY {"title":"Creating table 'group_user'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/group_user (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`group_id` INT(6) UNSIGNED NOT NULL DEFAULT 0,
	`user_id` INT(9) UNSIGNED NOT NULL DEFAULT 0,
	`expires` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `group_id` (`group_id`),
	KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'asset_files'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/asset_files;
/*@QUERY {"title":"Creating table 'asset_files'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/asset_files (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`extension` VARCHAR(20) DEFAULT NULL,
	`path` TEXT NOT NULL,
	`path_md5` CHAR(32) NOT NULL,
	`size` INT(10) NOT NULL DEFAULT 0,
	`width` INT(10) NOT NULL DEFAULT 0,
	`height` INT(10) NOT NULL DEFAULT 0,
	`info` TEXT,
	`filetype_id` INT(10) NOT NULL,
	`mtime` DATETIME DEFAULT NULL,
	`scanned` TINYINT(4) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `path_md5` (`path_md5`,`extension`,`name`),
	KEY `name` (`name`),
	KEY `ext` (`extension`),
	KEY `filetype_id` (`filetype_id`,`path_md5`),
	KEY `path` (`path`(333)),
	KEY `scanned` (`scanned`),
	FULLTEXT KEY `info` (`info`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'asset_file_types'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/asset_file_types;
/*@QUERY {"title":"Creating table 'asset_file_types'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/asset_file_types (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`type` VARCHAR(30) NOT NULL,
	`extensions` VARCHAR(255) NOT NULL,
	`path_prefix` VARCHAR(255) default NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `type` (`type`),
	KEY `extensions` (`extensions`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'asset_keywords'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/asset_keywords;
/*@QUERY {"title":"Creating table 'asset_keywords'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/asset_keywords (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) NOT NULL,
	`keyword` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `asset_id` (`asset_id`,`keyword`),
	KEY `keyword` (`keyword`),
	FULLTEXT KEY `fulltext_keyword` (`keyword`)
) ENGINE=MyISAM;

/*@QUERY {"title":"Dropping table 'related'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/related;
/*@QUERY {"title":"Creating table 'related'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/related (
  `article_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `related_article_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `relevance` TINYINT(4) NOT NULL DEFAULT 0,
  KEY `article_id` (`article_id`),
  KEY `related_article_id` (`related_article_id`),
  KEY `relevance` (`relevance`),
  KEY `article_id_relevance` (`article_id`, `relevance`)
) ENGINE=InnoDB;


/*@QUERY {"title":"Dropping table 'cache_tags'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/cache_tags;

/*@QUERY {"title":"Dropping table 'cache'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/cache;
/*@QUERY {"title":"Creating table 'cache'"}*/
CREATE TABLE IF NOT EXISTS /*{VIVVO_DB_PREFIX}*/cache (
  `id` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
  `expires` INT(4) UNSIGNED NOT NULL DEFAULT '0',
  `data` LONGTEXT COLLATE utf8_unicode_ci NOT NULL,
  `serialized` TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `expires` (`expires`),
  KEY `id_expires` (`id`,`expires`)
) ENGINE=InnoDB;

/*@QUERY {"title":"Creating table 'cache_tags'"}*/
CREATE TABLE IF NOT EXISTS `cache_tags` (
  `cache_id` VARCHAR(32) NOT NULL,
  `tag` VARCHAR(150) NOT NULL,
  KEY `tag` (`tag`),
  UNIQUE KEY `cache_id_tag` (`cache_id`,`tag`),
  CONSTRAINT `cache_tags_cache` FOREIGN KEY (`cache_id`) REFERENCES `/*{VIVVO_DB_PREFIX}*/cache` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;