-- Patch for email notification on new page creations
ALTER TABLE /*$wgDBprefix*/user ADD (user_enotif_newpages tinyint(1) NOT NULL default '0');
