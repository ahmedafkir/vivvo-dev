-- $Id: uninstall.sql 5361 2010-05-20 15:03:29Z krcko $

/*@QUERY {"title":"Removing table 'form_builder_forms'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/form_builder_forms;

/*@QUERY {"title":"Removing table 'form_builder_fields'"}*/
DROP TABLE IF EXISTS /*{VIVVO_DB_PREFIX}*/form_builder_fields;