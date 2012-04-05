-- $Id: install.sql 5361 2010-05-20 15:03:29Z krcko $

/*@QUERY {"title":"Dropping table 'maillist'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/maillist;

/*@QUERY {"title":"Creating table 'maillist'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/maillist (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(100) NOT NULL,
	`ip` VARCHAR(20) NOT NULL,
	`time` INT NOT NULL,
	`confirm` TINYINT NOT NULL,
	`domain_id` INT NOT NULL,
	`user_id` INT NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `confirm` (`confirm`),
	KEY `user_id` (`user_id`),
	KEY `domain_id` (`domain_id`)
) ENGINE = InnoDB;

/*@QUERY {"title":"Dropping table 'newsletter'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/newsletter;

/*@QUERY {"title":"Creating table 'newsletter'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/newsletter (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`subject` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`body` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`groups` VARCHAR(250) NULL DEFAULT NULL,
	`vte_template` TINYINT NULL DEFAULT 0,
	`test` TINYINT NULL DEFAULT 1,
	`test_email` VARCHAR(250) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `test` (`test`)
) ENGINE = InnoDB;

/*@QUERY {"title":"Adding new field to 'users' table"}*/
ALTER TABLE /*{VIVVO_DB_PREFIX}*/users
	ADD `subscriber` TINYINT NULL DEFAULT 0,
	ADD INDEX (`subscriber`);

/*@QUERY {"title":"Installing default newsletters"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/newsletter VALUES
(1, 'Most commented', '&lt;vte:template&gt;
&lt;vte:box module=&quot;box_article_list&quot;&gt;
&lt;vte:params&gt;&lt;vte:param name=&quot;cache&quot; value=&quot;1&quot; /&gt;
&lt;vte:param name=&quot;search_sort_by&quot; value=&quot;most_commented&quot; /&gt;
&lt;vte:param name=&quot;search_limit&quot; value=&quot;5&quot; /&gt;
&lt;/vte:params&gt;
&lt;vte:template&gt;&lt;vte:value select=&quot;{LNG_MOST_COMMENTED}&quot; /&gt;
&lt;vte:foreach item=&quot;article&quot;  key=&quot;index&quot; from=&quot;{article_list}&quot;&gt;
&lt;vte:value select=&quot;{article.get_title|convert2text}&quot; /&gt;
============================================================

&lt;vte:value select=&quot;{article.get_summary|convert2text}&quot; /&gt;

&lt;vte:value select=&quot;{VIVVO_URL}&quot; /&gt;&lt;vte:value select=&quot;{article.get_href}&quot; /&gt;

&lt;/vte:foreach&gt;
&lt;/vte:template&gt;
&lt;/vte:box&gt;
&lt;/vte:template&gt;', '-1', 1, 0, NULL),
(2, 'Most popular', '&lt;vte:template&gt;
&lt;vte:box module=&quot;box_article_list&quot;&gt;
&lt;vte:params&gt;&lt;vte:param name=&quot;cache&quot; value=&quot;1&quot; /&gt;
&lt;vte:param name=&quot;search_sort_by&quot; value=&quot;most_popular&quot; /&gt;
&lt;vte:param name=&quot;search_limit&quot; value=&quot;5&quot; /&gt;
&lt;/vte:params&gt;
&lt;vte:template&gt;&lt;vte:value select=&quot;{LNG_MOST_POPULAR}&quot; /&gt;
&lt;vte:foreach item=&quot;article&quot;  key=&quot;index&quot; from=&quot;{article_list}&quot;&gt;
&lt;vte:value select=&quot;{article.get_title|convert2text}&quot; /&gt;
============================================================

&lt;vte:value select=&quot;{article.get_summary|convert2text}&quot; /&gt;

&lt;vte:value select=&quot;{VIVVO_URL}&quot; /&gt;&lt;vte:value select=&quot;{article.get_href}&quot; /&gt;

&lt;/vte:foreach&gt;
&lt;/vte:template&gt;
&lt;/vte:box&gt;
&lt;/vte:template&gt;', '-1,2', 1, 0, NULL),
(3, 'Latest news (last 7 days, limit 50 articles)', '&lt;vte:template&gt;
&lt;vte:box module=&quot;box_article_list&quot;&gt;
&lt;vte:params&gt;&lt;vte:param name=&quot;cache&quot; value=&quot;1&quot; /&gt;
&lt;vte:param name=&quot;search_search_date&quot; value=&quot;7&quot; /&gt;
&lt;vte:param name=&quot;search_before_after&quot; value=&quot;1&quot; /&gt;
&lt;vte:param name=&quot;search_limit&quot; value=&quot;50&quot; /&gt;
&lt;/vte:params&gt;
&lt;vte:template&gt;&lt;vte:value select=&quot;{LNG_LATEST_NEWS}&quot; /&gt;:
&lt;vte:foreach item=&quot;article&quot;  key=&quot;index&quot; from=&quot;{article_list}&quot;&gt;
&lt;vte:value select=&quot;{article.get_title|convert2text}&quot; /&gt;
============================================================

&lt;vte:value select=&quot;{article.get_created}&quot; /&gt;
&lt;vte:value select=&quot;{article.get_summary|convert2text}&quot; /&gt;

&lt;vte:value select=&quot;{VIVVO_URL}&quot; /&gt;&lt;vte:value select=&quot;{article.get_href}&quot; /&gt;

&lt;/vte:foreach&gt;
&lt;/vte:template&gt;
&lt;/vte:box&gt;
&lt;/vte:template&gt;', '-1,2', 1, 0, NULL);
