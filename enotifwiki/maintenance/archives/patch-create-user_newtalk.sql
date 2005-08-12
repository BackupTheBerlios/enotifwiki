-- Entries for newtalk on user_talk page are
-- handled in table user_newtalk for efficient memcaching
-- but are also handled as entries in the watchlist table for email notifications
CREATE TABLE /*$wgDBprefix*/user_newtalk (
  user_id int(5) NOT NULL default '0',
  user_ip varchar(40) NOT NULL default '',
  INDEX user_id (user_id),
  INDEX user_ip (user_ip)
);