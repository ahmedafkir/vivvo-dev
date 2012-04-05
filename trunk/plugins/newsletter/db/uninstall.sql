-- $Id: uninstall.sql 5361 2010-05-20 15:03:29Z krcko $

/*@QUERY {"title":"Removing table 'maillist'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/maillist;

/*@QUERY {"title":"Removing table 'newsletter'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/newsletter;

/*@QUERY {"title":"Removing field from 'users' table"}*/
ALTER TABLE /*{VIVVO_DB_PREFIX}*/users DROP `subscriber`;