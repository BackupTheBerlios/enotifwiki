--
-- E-mail confirmation token and expiration timestamp,
-- for verification of e-mail addresses.
--
-- 2005-04-25
--

ALTER TABLE /*$wgDBprefix*/user
  ADD COLUMN user_email_token CHAR(32) BINARY;
