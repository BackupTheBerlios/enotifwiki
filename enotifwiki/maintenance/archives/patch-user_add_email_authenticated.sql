--
-- E-mail confirmation token and expiration timestamp,
-- for verification of e-mail addresses.
--
-- 2005-05-05
--

ALTER TABLE /*$wgDBprefix*/user
  ADD COLUMN user_email_authenticated CHAR(14) BINARY;
