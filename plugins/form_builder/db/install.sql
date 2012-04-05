-- $Id: install.sql 5361 2010-05-20 15:03:29Z krcko $

/*@QUERY {"title":"Dropping table 'form_builder_forms'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/form_builder_forms;

/*@QUERY {"title":"Creating table 'form_builder_forms'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/form_builder_forms (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL,
  `email` VARCHAR(100) CHARACTER SET latin1 NULL DEFAULT NULL,
  `status` TINYINT NOT NULL DEFAULT 0,
  `message` TEXT COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `user_status` TINYINT NOT NULL DEFAULT 0,
  `message_url` VARCHAR(200) NULL DEFAULT NULL,
  `action` VARCHAR(50) CHARACTER SET latin1 NULL DEFAULT NULL,
  `cmd` VARCHAR(50) CHARACTER SET latin1 NULL DEFAULT NULL,
  `url` VARCHAR(50) CHARACTER SET latin1 NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `user_status` (`user_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*@QUERY {"title":"Dropping table 'form_builder_fields'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/form_builder_fields;

/*@QUERY {"title":"Creating table 'form_builder_fields'"}*/
CREATE TABLE /*{VIVVO_DB_PREFIX}*/form_builder_fields (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(100) CHARACTER SET latin1 NOT NULL,
  `form_id` INT NOT NULL,
  `order_number` INT NOT NULL,
  `name` VARCHAR(50) CHARACTER SET latin1 NULL DEFAULT NULL,
  `label` VARCHAR(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `required` TINYINT NULL DEFAULT 0,
  `size` INT NULL DEFAULT NULL,
  `max_size` INT NULL DEFAULT NULL,
  `reg_exp` VARCHAR(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `description` TEXT COLLATE utf8_unicode_ci,
  `options` VARCHAR(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `selected` VARCHAR(255) COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `error_message` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`),
  KEY `order_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*@QUERY {"title":"Creating demo form"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/form_builder_forms (`id`, `title`, `email`, `status`, `message`, `user_status`, `message_url`, `action`, `cmd`, `url`) VALUES
(1, 'Contact Us', 'admin@website.com', 1, 'Thanks for contacting us!', 0, NULL, 'form_builder', 'mail', 'contact');

/*@QUERY {"title":"Inserting form fields"}*/
INSERT INTO /*{VIVVO_DB_PREFIX}*/form_builder_fields (`id`, `type`, `form_id`, `order_number`, `name`, `label`, `required`, `size`, `max_size`, `reg_exp`, `description`, `options`, `selected`, `error_message`) VALUES
(1, 'input', 1, 2, 'field_1', 'First Name', 1, 0, 0, NULL, NULL, NULL, NULL, NULL),
(2, 'input', 1, 3, 'field_2', 'Last Name', 1, 0, 0, NULL, NULL, NULL, NULL, NULL),
(3, 'submit', 1, 7, 'field_3', 'Send', 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(4, 'input', 1, 4, 'field_4', 'Email address', 1, 0, 0, NULL, NULL, NULL, NULL, NULL),
(5, 'textarea', 1, 6, 'field_5', 'Message', 0, 0, NULL, NULL, 'Write your message here', NULL, ' ', NULL),
(6, 'drop_down', 1, 5, 'field_6', 'Why are you writing?', 0, NULL, 0, NULL, 'Choose the reason', 'Just to say hello\nI have a question\nThere''s a problem with your website', 'Just to say hello', NULL);