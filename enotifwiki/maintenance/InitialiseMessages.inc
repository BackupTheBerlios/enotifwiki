<?php
/**
 * Script to initialise the MediaWiki namespace
 *
 * This script is included from update.php and install.php. Do not run it 
 * by itself.
 *
 * @deprecated
 * @package MediaWiki
 * @subpackage Maintenance
 */

/** */
function initialiseMessages( $overwrite = false, $messageArray = false ) {
	global $wgContLang, $wgContLanguageCode;
	global $wgContLangClass, $wgAllMessagesEn;
	global $wgDisableLangConversion;
	global $wgForceUIMsgAsContentMsg;
	global $wgLanguageNames;
	global $IP;

	# overwrite language conversion	option so that all variants 
	# of the messages are initialised
	$wgDisableLangConversion = false;

	if ( $messageArray ) {
		$sortedArray = $messageArray;
	} else {
		$sortedArray = $wgAllMessagesEn;
	}
	
	ksort( $sortedArray );
	$messages=array();

	$variants = $wgContLang->getVariants();
	if(!in_array($wgContLanguageCode, $variants))
		$variants[]=$wgContLanguageCode;

	foreach ($variants as $v) {
		$langclass = 'Language'. str_replace( '-', '_', ucfirst( $v ) );
		if( !class_exists($langclass) ) {
			die ("class $langclass not defined. perhaps you need to include the file $langclass.php in $wgContLangClass.php?");
		}
		$lang = new $langclass;

		if($v==$wgContLanguageCode)
			$suffix='';
		else
			$suffix="/$v";
		foreach ($sortedArray as $key => $msg) {
			$messages[$key.$suffix] = $lang->getMessage($key);
		}
	}

	require_once('languages/Names.php');

    /*
	  initialize all messages in $wgForceUIMsgAsContentMsg for all
	  languages in Names.php
    */
	if( is_array( $wgForceUIMsgAsContentMsg ) ) {
		foreach( $wgForceUIMsgAsContentMsg as $uikey ) {
			foreach( $wgLanguageNames as $code => $name) {
				if( $code == $wgContLanguageCode )
					continue;
				$msg = $wgContLang->getMessage( $uikey );
				if( $msg )
					$messages[$uikey. '/' . $code] = $msg;
			}
		}
	}
	initialiseMessagesReal( $overwrite, $messages );
}

/** */
function initialiseMessagesReal( $overwrite = false, $messageArray = false ) {
	global $wgContLang, $wgScript, $wgServer, $wgAllMessagesEn;
	global $wgOut, $wgArticle, $wgUser;
	global $wgMessageCache, $wgMemc, $wgDBname, $wgUseMemCached;

	# Initialise $wgOut and $wgUser for a command line script
	$wgOut->disable();

	$wgUser = new User;
	$wgUser->setLoaded( true ); # Don't load from DB
	$wgUser->setName( 'MediaWiki default' );
	
	# Don't try to draw messages from the database we're initialising
	$wgMessageCache->disable();
	$wgMessageCache->disableTransform();

	$fname = 'initialiseMessages';
	$ns = NS_MEDIAWIKI;
	# cur_user_text responsible for the modifications
	# Don't change it unless you're prepared to update the DBs accordingly, otherwise the 
	# default messages won't be overwritte
	$username = 'MediaWiki default';

	
	print "Initialising \"MediaWiki\" namespace...\n";

	
	$dbr =& wfGetDB( DB_SLAVE );
	$dbw =& wfGetDB( DB_MASTER );
	$page = $dbr->tableName( 'page' );
	$revision = $dbr->tableName( 'revision' );

	$timestamp = wfTimestampNow();

	#$sql = "SELECT cur_title,cur_is_new,cur_user_text FROM $cur WHERE cur_namespace=$ns AND cur_title IN(";
	$sql = "SELECT page_title,page_is_new,rev_user_text FROM $page, $revision WHERE
		page_namespace=$ns AND rev_page=page_id AND page_title IN(";

	# Get keys from $wgAllMessagesEn, which is more complete than the local language
	$first = true;
	if ( $messageArray ) {
		$sortedArray = $messageArray;
	} else {
		$sortedArray = $wgAllMessagesEn;
	}
	
	ksort( $sortedArray );

	# SELECT all existing messages
	# Can't afford to be locking all rows for update, this script can take quite a long time to complete
	foreach ( $sortedArray as $key => $enMsg ) {
		if ( $key == '' ) {
			continue; // Skip odd members
		}
		if ( $first ) {
			$first = false;
		} else {
			$sql .= ',';
		}
		$titleObj = Title::newFromText( $wgContLang->ucfirst( $key ) );
		$enctitle = $dbr->strencode($titleObj->getDBkey());
		$sql .= "'$enctitle'";
	}
	$sql .= ')';
	$res = $dbr->query( $sql );
	$row = $dbr->fetchObject( $res );

	# Read the results into an array
	# Decide whether or not each one needs to be overwritten
	$existingTitles = array();
	while ( $row ) {
		if ( $row->rev_user_text != $username  && $row->rev_user_text != 'Template namespace initialisation script' ) {
			$existingTitles[$row->page_title] = 'keep';
		} else {
			$existingTitles[$row->page_title] = 'chuck';
		}

		$row = $dbr->fetchObject( $res );
	}

	# Insert queries are done in one multi-row insert
	# Here's the start of it:
	$arr = array();
	$talk = $wgContLang->getNsText( NS_TALK );
	$mwtalk = $wgContLang->getNsText( NS_MEDIAWIKI_TALK );
	
	# Process each message
	foreach ( $sortedArray as $key => $enMsg ) {
		if ( $key == '' ) {
			continue; // Skip odd members
		}
		# Get message text
		if ( $messageArray ) {
			$message = $enMsg;
		} else {
			$message = wfMsgNoDBForContent( $key );
		}
		$titleObj = Title::newFromText( $wgContLang->ucfirst( $key ), NS_MEDIAWIKI );
		$title = $titleObj->getDBkey();

		# Update messages which already exist
		if ( array_key_exists( $title, $existingTitles ) ) {
			if ( $existingTitles[$title] == 'chuck' || $overwrite) {
				# Don't bother writing a new revision if we're the same
				# as the current text!
				$revision = Revision::newFromTitle( $titleObj );
				if( is_null( $revision ) || $revision->getText() != $message ) {
					$article = new Article( $titleObj );
					$article->quickEdit( $message );
				}
			}
		} else {
			$article = new Article( $titleObj );
			$newid = $article->insertOn( $dbw, 'sysop' );
			# FIXME: set restrictions
			$revision = new Revision( array(
				'page'      => $newid,
				'text'      => $message,
				'user'      => 0,
				'user_text' => $username,
				'comment'   => '',
				) );
			$revid = $revision->insertOn( $dbw );
			$article->updateRevisionOn( $dbw, $revision );
		}
	}

	# Clear the relevant memcached key
	print 'Clearing message cache...';
	$wgMessageCache->clear();
	print "Done.\n";
}

/** */
function loadLanguageFile( $filename ) {
	$contents = file_get_contents( $filename );
	# Remove header line
	$p = strpos( $contents, "\n" ) + 1;
	$contents = substr( $contents, $p );
	# Unserialize
	return unserialize( $contents );
}

/** */
function doUpdates() {
	global $wgDeferredUpdateList;
	foreach ( $wgDeferredUpdateList as $up ) { $up->doUpdate(); }
}
?>
