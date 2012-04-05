/*@QUERY {"title":"Adding new field to 'articles' table"}*/
ALTER TABLE /*{VIVVO_DB_PREFIX}*/articles
	ADD  `video_attachment` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
	ADD INDEX (`video_attachment`(20));