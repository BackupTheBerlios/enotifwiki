-- Patch for allowing direct links to difference views between
-- current and last-seen revisions of watched pages
-- records rc_last_oldid of the last revision the user saw
ALTER TABLE /*$wgDBprefix*/watchlist ADD (wl_lastvisitedrevision int(10) unsigned NOT NULL default '0');
