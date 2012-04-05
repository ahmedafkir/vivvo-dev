-- $Id: uninstall.sql 5361 2010-05-20 15:03:29Z krcko $

/*@QUERY {"title":"Removing table 'feeds'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/feeds;

/*@QUERY {"title":"Removing field from 'articles' table"}*/
ALTER TABLE /*{VIVVO_DB_PREFIX}*/articles DROP `feed_item_id`;