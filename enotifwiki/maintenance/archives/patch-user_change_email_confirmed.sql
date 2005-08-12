-- Added early in Enotif 1.5 alpha development, renamed to MediaWiki standard column name 2005-06-25

ALTER TABLE /*$wgDBprefix*/user CHANGE user_email_confirmed user_email_authenticated CHAR(14) BINARY;
