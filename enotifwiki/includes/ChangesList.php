<?php
/**
 * @package MediaWiki
 */

/**
 * @package MediaWiki
 */
class ChangesList {
	# Called by history lists and recent changes
	#

	/** @todo document */
	function ChangesList( &$skin ) {
		$this->skin =& $skin;
	}

	/**
	 * Returns the appropiate flags for new page, minor change and patrolling
	 */
	function recentChangesFlags( $new, $minor, $patrolled, $nothing = '&nbsp;' ) {
		$f = $new ? '<span class="newpage">' . htmlspecialchars( wfMsg( 'newpageletter' ) ) . '</span>'
				: $nothing;
		$f .= $minor ? '<span class="minor">' . htmlspecialchars( wfMsg( 'minoreditletter' ) ) . '</span>'
				: $nothing;
		$f .= $patrolled ? '<span class="unpatrolled">!</span>' : $nothing;
		return $f;

	}

	/**
	 * Returns text for the start of the tabular part of RC
	 */
	function beginRecentChangesList() {
		$this->rc_cache = array() ;
		$this->rcMoveIndex = 0;
		$this->rcCacheIndex = 0 ;
		$this->lastdate = '';
		$this->rclistOpen = false;
		return '';
	}

	/**
 	 * Returns text for the end of RC
	 * If enhanced RC is in use, returns pretty much all the text
	 */
	function endRecentChangesList() {
		$s = $this->recentChangesBlock() ;
		if( $this->rclistOpen ) {
			$s .= "</ul>\n";
		}
		return $s;
	}

	/**
	 * Enhanced RC ungrouped line
	 */
	function recentChangesBlockLine ( $rcObj ) {
		global $wgStylePath, $wgContLang ;

		# Get rc_xxxx variables
		extract( $rcObj->mAttribs ) ;
		$curIdEq = 'curid='.$rc_cur_id;

		# Spacer image
		$r = '' ;

		$r .= '<img src="'.$wgStylePath.'/common/images/Arr_.png" width="12" height="12" border="0" />' ;
		$r .= '<tt>' ;

		if ( $rc_type == RC_MOVE || $rc_type == RC_MOVE_OVER_REDIRECT ) {
			$r .= '&nbsp;&nbsp;&nbsp;';
		} else {
			$r .= $this->recentChangesFlags( $rc_type == RC_NEW, $rc_minor, $rcObj->unpatrolled );
		}

		# Timestamp
		$r .= ' '.$rcObj->timestamp.' ' ;
		$r .= '</tt>' ;

		# Article link
		$link = $rcObj->link ;
		if ( $rcObj->watched ) $link = '<strong>'.$link.'</strong>' ;
		$r .= $link ;

		if ( $rcObj->notificationtimestamp ) {
			$r .= $this->skin->makeKnownLinkObj( $rcObj->getTitle(), wfMsg( 'updatedmarker' ),
			  "diff=0&oldid={$rcObj->lastvisitedrevision}", '', '', '', wfMsg( 'updatedmarker_tooltiptext' ) );
		}

		# Diff
		$r .= ' (' ;
		$r .= $rcObj->difflink ;
		if ($rcObj->lvrlink) {
			$r .= "; ".$rcObj->lvrlink ;
		}
		$r .= '; ' ;

		# Hist
		$r .= $this->skin->makeKnownLinkObj( $rcObj->getTitle(), wfMsg( 'hist' ), $curIdEq.'&action=history', '', '','', wfMsg( 'hist_tooltiptext' ));


		# User/talk
		$r .= ') . . '.$rcObj->userlink ;
		$r .= $rcObj->usertalklink ;

		# Comment
		 if ( $rc_type != RC_MOVE && $rc_type != RC_MOVE_OVER_REDIRECT ) {
			$r .= $this->skin->commentBlock( $rc_comment, $rcObj->getTitle() );
		}

		if ($rcObj->numberofWatchingusers > 0) {
			$r .= ' ' . wfMsg('number_of_watching_users_RCview',  $wgContLang->formatNum($rcObj->numberofWatchingusers));
		}

		$r .= "<br />\n" ;
		return $r ;
	}

	/**
	 * Enhanced RC group
	 */
	function recentChangesBlockGroup ( $block ) {
		global $wgStylePath, $wgContLang ;

		$r = '';

		# Collate list of users
		$isnew = false ;
		$unpatrolled = false;
		$userlinks = array () ;
		foreach ( $block AS $rcObj ) {
			$oldid = $rcObj->mAttribs['rc_last_oldid'];
			$newid = $rcObj->mAttribs['rc_this_oldid'];
			if ( $rcObj->mAttribs['rc_new'] ) {
				$isnew = true ;
			}
			$u = $rcObj->userlink ;
			if ( !isset ( $userlinks[$u] ) ) {
				$userlinks[$u] = 0 ;
			}
			if ( $rcObj->unpatrolled ) {
				$unpatrolled = true;
			}
			$userlinks[$u]++ ;
		}

		# Sort the list and convert to text
		krsort ( $userlinks ) ;
		asort ( $userlinks ) ;
		$users = array () ;
		foreach ( $userlinks as $userlink => $count) {
			$text = $userlink ;
			if ( $count > 1 ) $text .= " ({$count}&times;)" ;
			array_push ( $users , $text ) ;
		}
		$users = ' <span class="changedby">['.implode('; ',$users).']</span>';

		# Arrow
		$rci = 'RCI'.$this->rcCacheIndex ;
		$rcl = 'RCL'.$this->rcCacheIndex ;
		$rcm = 'RCM'.$this->rcCacheIndex ;
		$toggleLink = "javascript:toggleVisibility('$rci','$rcm','$rcl')" ;
		$arrowdir = $wgContLang->isRTL() ? 'l' : 'r';
		$tl  = '<span id="'.$rcm.'"><a href="'.$toggleLink.'"><img src="'.$wgStylePath.'/common/images/Arr_'.$arrowdir.'.png" width="12" height="12" alt="+" /></a></span>' ;
		$tl .= '<span id="'.$rcl.'" style="display:none"><a href="'.$toggleLink.'"><img src="'.$wgStylePath.'/common/images/Arr_d.png" width="12" height="12" alt="-" /></a></span>' ;
		$r .= $tl ;

		# Main line

		$r .= '<tt>' ;
		$r .= $this->recentChangesFlags( $isnew, false, $unpatrolled );

		# Timestamp
		$r .= ' '.$block[0]->timestamp.' ' ;
		$r .= '</tt>' ;

		# Article link
		$link = $block[0]->link ;
		if ( $block[0]->watched ) $link = '<strong>'.$link.'</strong>' ;
		$r .= $link ;

		if ( $block[0]->notificationtimestamp ) {
			$r .= $this->skin->makeKnownLinkObj( $rcObj->getTitle(), wfMsg( 'updatedmarker' ),
			  "diff=0&oldid={$rcObj->lastvisitedrevision}", '', '', '', wfMsg( 'updatedmarker_tooltiptext' ) );
		}

		$curIdEq = 'curid=' . $block[0]->mAttribs['rc_cur_id'];
		$currentRevision = $block[0]->mAttribs['rc_this_oldid'];
		if ( $block[0]->mAttribs['rc_type'] != RC_LOG ) {
			# Changes
			$r .= ' ('.count($block).' ' ;
			if ( $isnew ) $r .= wfMsg('changes');
			else $r .= $this->skin->makeKnownLinkObj( $block[0]->getTitle() , wfMsg('changes') ,
				$curIdEq."&diff=$currentRevision&oldid=$oldid" ) ;
			$r .= '; ' ;

			# History
			$r .= $this->skin->makeKnownLinkObj( $block[0]->getTitle(), wfMsg( 'history' ), $curIdEq.'&action=history' );
			if ($block[0]->lvrlink) {
				$r .= "; ".$block[0]->lvrlink;
			}
			$r .= ')' ;
		}

		$r .= $users ;

		if ($block[0]->numberofWatchingusers > 0) {
			$r .= ' ' . wfMsg('number_of_watching_users_RCview', $wgContLang->formatNum($block[0]->numberofWatchingusers));
		}
		$r .= "<br />\n" ;

		# Sub-entries
		$r .= '<div id="'.$rci.'" style="display:none">' ;
		foreach ( $block AS $rcObj ) {
			# Get rc_xxxx variables
			extract( $rcObj->mAttribs );

			$r .= '<img src="'.$wgStylePath.'/common/images/Arr_.png" width="12" height="12" />';
			$r .= '<tt>&nbsp; &nbsp; &nbsp; &nbsp;' ;
			$r .= $this->recentChangesFlags( $rc_new, $rc_minor, $rcObj->unpatrolled );
			$r .= '&nbsp;</tt>' ;

			$o = '' ;
			if ( $rc_last_oldid != 0 ) {
				$o = 'oldid='.$rc_last_oldid ;
			}
			if ( $rc_type == RC_LOG ) {
				$link = $rcObj->timestamp ;
			} else {
				$link = $this->skin->makeKnownLinkObj( $rcObj->getTitle(), $rcObj->timestamp , "{$curIdEq}&$o" ) ;
			}
			$link = '<tt>'.$link.'</tt>' ;

			$r .= $link ;

			if ( $rcObj->notificationtimestamp ) {
				$r .= $this->skin->makeKnownLinkObj( $rcObj->getTitle(), wfMsg( 'updatedmarker' ),
				  "diff=0&oldid={$rcObj->lastvisitedrevision}", '', '', '', wfMsg( 'updatedmarker_tooltiptext' ) );
			}

			$r .= ' (' ;
			$r .= $rcObj->curlink ;
			$r .= '; ' ;
			$r .= $rcObj->lastlink ;
			$r .= ') . . '.$rcObj->userlink ;
			$r .= $rcObj->usertalklink ;
			$r .= $this->skin->commentBlock( $rc_comment, $rcObj->getTitle() );
			$r .= "<br />\n" ;
		}
		$r .= "</div>\n" ;

		$this->rcCacheIndex++ ;
		return $r ;
	}

	/**
	 * If enhanced RC is in use, this function takes the previously cached
	 * RC lines, arranges them, and outputs the HTML
	 */
	function recentChangesBlock () {
		global $wgStylePath ;
		if ( count ( $this->rc_cache ) == 0 ) return '' ;
		$blockOut = '';
		foreach ( $this->rc_cache AS $secureName => $block ) {
			if ( count ( $block ) < 2 ) {
				$blockOut .= $this->recentChangesBlockLine ( array_shift ( $block ) ) ;
			} else {
				$blockOut .= $this->recentChangesBlockGroup ( $block ) ;
			}
		}

		return '<div>'.$blockOut.'</div>' ;
	}

	/**
	 * Called in a loop over all displayed RC entries
	 * Either returns the line, or caches it for later use
	 */
	function recentChangesLine( &$rc, $watched = false ) {
		global $wgUser;
		$usenew = $wgUser->getOption( 'usenewrc' );
		if ( $usenew )
			$line = $this->recentChangesLineNew ( $rc, $watched ) ;
		else
			$line = $this->recentChangesLineOld ( $rc, $watched ) ;
		return $line ;
	}


	function recentChangesLineOld( &$rc, $watched = false ) {
		global $wgTitle, $wgLang, $wgContLang, $wgUser, $wgUseRCPatrol,
			$wgOnlySysopsCanPatrol, $wgSysopUserBans;

		$fname = 'Skin::recentChangesLineOld';
		wfProfileIn( $fname );

		static $message;
		if( !isset( $message ) ) {
			foreach( explode(' ', 'diff diff_tooltiptext hist hist_tooltiptext minoreditletter newpageletter blocklink undo '.
						'diff-to-lvr lvr diff-to-lvr_tooltiptext lvr_tooltiptext updatedmarker updatedmarker_tooltiptext ' ) as $msg ) {
				$message[$msg] = wfMsg( $msg );
			}
		}

		# Extract DB fields into local scope
		extract( $rc->mAttribs );
		$curIdEq = 'curid=' . $rc_cur_id;

		# Should patrol-related stuff be shown?
		$unpatrolled = $wgUseRCPatrol && $wgUser->isLoggedIn() &&
		  ( !$wgOnlySysopsCanPatrol || $wgUser->isAllowed('patrol') ) && $rc_patrolled == 0;

		# Make date header if necessary
		$date = $wgLang->date( $rc_timestamp, true, true );
		$s = '';
		if ( $date != $this->lastdate ) {
			if ( '' != $this->lastdate ) { $s .= "</ul>\n"; }
			$s .= "<h4>{$date}</h4>\n<ul class=\"special\">";
			$this->lastdate = $date;
			$this->rclistOpen = true;
		}

		$s .= '<li>';

		if ( $rc_type == RC_MOVE || $rc_type == RC_MOVE_OVER_REDIRECT ) {
			# Diff
			$s .= '(' . $message['diff'] . ') (';
			# Hist
			$s .= $this->skin->makeKnownLinkObj( $rc->getMovedToTitle(), $message['hist'], 'action=history', '', '', '', $message['hist_tooltiptext'] ) .
				') . . ';

			# "[[x]] moved to [[y]]"
			$msg = ( $rc_type == RC_MOVE ) ? '1movedto2' : '1movedto2_redir';
			$s .= wfMsg( $msg, $this->skin->makeKnownLinkObj( $rc->getTitle(), '', 'redirect=no' ),
				$this->skin->makeKnownLinkObj( $rc->getMovedToTitle(), '' ) );
		} elseif( $rc_namespace == NS_SPECIAL && preg_match( '!^Log/(.*)$!', $rc_title, $matches ) ) {
			# Log updates, etc
			$logtype = $matches[1];
			$logname = LogPage::logName( $logtype );
			$s .= '(' . $this->skin->makeKnownLinkObj( $rc->getTitle(), $logname ) . ')';
		} else {
			wfProfileIn("$fname-page");
			# Diff link
			if ( $rc_type == RC_NEW || $rc_type == RC_LOG ) {
				$diffLink = $message['diff'];
			} else {
				if ( $unpatrolled )
					$rcidparam = "&rcid={$rc_id}";
				else
					$rcidparam = "";
				$diffLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), $message['diff'],
				  "{$curIdEq}&diff={$rc_this_oldid}&oldid={$rc_last_oldid}{$rcidparam}",
				  '', '', ' tabindex="'.$rc->counter.'"', $message['diff_tooltiptext'] );
			}

			if ( $watched && $rc->lastvisitedrevision )  {
				if ( $rc_this_oldid == $rc->lastvisitedrevision ) {
					$lvrLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), $message['lvr'],
					  "diff={$rc_this_oldid}&oldid={$rc->lastvisitedrevision}", '','','', $message['lvr_tooltiptext']);
				} else {
					$lvrLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), $message['diff-to-lvr'],
					  "diff={$rc_this_oldid}&oldid={$rc->lastvisitedrevision}",'','','', $message['diff-to-lvr_tooltiptext'] );
				}
			} else {
				$lvrLink = $message['diff-to-lvr'];
			}
			$s .= '('.$diffLink.') ('.$lvrLink.') (';

			# History link
			$s .= $this->skin->makeKnownLinkObj( $rc->getTitle(), $message['hist'], $curIdEq.'&action=history', '', '', '', $message['hist_tooltiptext'] );
			$s .= ') . . ';

			# M, N and ! (minor, new and unpatrolled)
			$s .= ' ' . $this->recentChangesFlags( $rc_type == RC_NEW, $rc_minor, $unpatrolled, '' );

			# Article link
			# If it's a new article, there is no diff link, but if it hasn't been
			# patrolled yet, we need to give users a way to do so
			if ( $unpatrolled && $rc_type == RC_NEW )
				$articleLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), '', "rcid={$rc_id}" );
			else
				$articleLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), '' );

			if ( $watched ) {
				$articleLink = '<strong>'.$articleLink.'</strong>';
			}

			if ( $rc->notificationtimestamp ) {
				$articleLink .= $this->skin->makeKnownLinkObj( $rc->getTitle(), $message['updatedmarker'],
				  "diff=0&oldid={$rc->lastvisitedrevision}",'','','', $message['updatedmarker_tooltiptext'] );
			}

			$s .= ' '.$articleLink;
			wfProfileOut("$fname-page");
		}

		wfProfileIn( "$fname-rest" );
		# Timestamp
		$s .= '; ' . $wgLang->time( $rc_timestamp, true, true ) . ' . . ';

		# User link (or contributions for unregistered users)
		if ( 0 == $rc_user ) {
			$contribsPage =& Title::makeTitle( NS_SPECIAL, 'Contributions' );
			$userLink = $this->skin->makeKnownLinkObj( $contribsPage,
				$rc_user_text, 'target=' . $rc_user_text );
		} else {
			$userPage =& Title::makeTitle( NS_USER, $rc_user_text );
			$userLink = $this->skin->makeLinkObj( $userPage, htmlspecialchars( $rc_user_text ) );
		}
		$s .= $userLink;

		# User talk link
		$talkname = $wgContLang->getNsText(NS_TALK); # use the shorter name
		global $wgDisableAnonTalk;
		if( 0 == $rc_user && $wgDisableAnonTalk ) {
			$userTalkLink = '';
		} else {
			$userTalkPage =& Title::makeTitle( NS_USER_TALK, $rc_user_text );
			$userTalkLink= $this->skin->makeLinkObj( $userTalkPage, htmlspecialchars( $talkname ) );
		}
		# Block link
		$blockLink='';
		if ( ( $wgSysopUserBans || 0 == $rc_user ) && $wgUser->isAllowed('block') ) {
			$blockLinkPage = Title::makeTitle( NS_SPECIAL, 'Blockip' );
			$blockLink = $this->skin->makeKnownLinkObj( $blockLinkPage,
				htmlspecialchars( $message['blocklink'] ), 'ip=' . urlencode( $rc_user_text ) );

		}
		if($blockLink) {
			if($userTalkLink) $userTalkLink .= ' | ';
			$userTalkLink .= $blockLink;
		}
		if($userTalkLink) $s.=' ('.$userTalkLink.')';

		# Add comment
		if ( $rc_type != RC_MOVE && $rc_type != RC_MOVE_OVER_REDIRECT ) {
			$s .= $this->skin->commentBlock( $rc_comment, $rc->getTitle() );
		}

		if ($rc->numberofWatchingusers > 0) {
			$s .= ' ' . wfMsg('number_of_watching_users_RCview', $wgContLang->formatNum($rc->numberofWatchingusers));
		}

		$s .= "</li>\n";

		wfProfileOut( "$fname-rest" );
		wfProfileOut( $fname );
		return $s;
	}

	function recentChangesLineNew( &$baseRC, $watched = false ) {
		global $wgTitle, $wgLang, $wgContLang, $wgUser,
			$wgUseRCPatrol, $wgOnlySysopsCanPatrol, $wgSysopUserBans;

		static $message;
		if( !isset( $message ) ) {
			foreach( explode(' ', 'cur diff hist minoreditletter newpageletter last blocklink undo diff-to-lvr '.
				'diff-to-lvr_tooltiptext cur_tooltiptext diff_tooltiptext last_tooltiptext updatedmarker updatedmarker_tooltiptext ' ) as $msg ) {
				$message[$msg] = wfMsg( $msg );
			}
		}

		# Create a specialised object
		$rc = RCCacheEntry::newFromParent( $baseRC ) ;

		# Extract fields from DB into the function scope (rc_xxxx variables)
		extract( $rc->mAttribs );
		$curIdEq = 'curid=' . $rc_cur_id;

		# If it's a new day, add the headline and flush the cache
		$date = $wgLang->date( $rc_timestamp, true);
		$ret = '';
		if ( $date != $this->lastdate ) {
			# Process current cache
			$ret = $this->recentChangesBlock () ;
			$this->rc_cache = array() ;
			$ret .= "<h4>{$date}</h4>\n";
			$this->lastdate = $date;
		}

		# Should patrol-related stuff be shown?
		if ( $wgUseRCPatrol && $wgUser->isLoggedIn() &&
		  ( !$wgOnlySysopsCanPatrol || $wgUser->isAllowed('patrol') )) {
		  	$rc->unpatrolled = !$rc_patrolled;
		} else {
			$rc->unpatrolled = false;
		}

		# Make article link
		if ( $rc_type == RC_MOVE || $rc_type == RC_MOVE_OVER_REDIRECT ) {
			$msg = ( $rc_type == RC_MOVE ) ? "1movedto2" : "1movedto2_redir";
			$clink = wfMsg( $msg, $this->skin->makeKnownLinkObj( $rc->getTitle(), '', 'redirect=no' ),
			  $this->skin->makeKnownLinkObj( $rc->getMovedToTitle(), '' ) );
		} elseif( $rc_namespace == NS_SPECIAL && preg_match( '!^Log/(.*)$!', $rc_title, $matches ) ) {
			# Log updates, etc
			$logtype = $matches[1];
			$logname = LogPage::logName( $logtype );
			$clink = '(' . $this->skin->makeKnownLinkObj( $rc->getTitle(), $logname ) . ')';
		} elseif ( $rc->unpatrolled && $rc_type == RC_NEW ) {
			# Unpatrolled new page, give rc_id in query
			$clink = $this->skin->makeKnownLinkObj( $rc->getTitle(), '', "rcid={$rc_id}" );
		} else {
			$clink = $this->skin->makeKnownLinkObj( $rc->getTitle(), '' ) ;
		}

		$time = $wgContLang->time( $rc_timestamp, true, true );
		$rc->watched = $watched ;
		$rc->link = $clink ;
		$rc->timestamp = $time;
		$rc->numberofWatchingusers = $baseRC->numberofWatchingusers;
		$rc->lastvisitedrevision   = $baseRC->lastvisitedrevision;
		$rc->notificationtimestamp = $baseRC->notificationtimestamp;


		# Make "cur" and "diff" links
		$titleObj = $rc->getTitle();
		if ( $rc->unpatrolled ) {
			$rcIdQuery = "&rcid={$rc_id}";
		} else {
			$rcIdQuery = '';
		}
		$query = $curIdEq."&diff=$rc_this_oldid&oldid=$rc_last_oldid";
		$aprops = ' tabindex="'.$baseRC->counter.'"';
		$curLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), $message['cur'], $query, '' ,'' , $aprops );
		if( $rc_type == RC_NEW || $rc_type == RC_LOG || $rc_type == RC_MOVE || $rc_type == RC_MOVE_OVER_REDIRECT ) {
			if( $rc_type != RC_NEW ) {
				$curLink = $message['cur'];
			}
			$diffLink = $message['diff'];
		} else {
		#	$query = $curIdEq.'&diff=0&oldid='.$rc_this_oldid;
			$query = $curIdEq."&diff=$rc_this_oldid&oldid=$rc_last_oldid";
			$aprops = ' tabindex="'.$baseRC->counter.'"';
			$curLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), $message['cur'], $query, '', '', $aprops, $message['cur_tooltiptext'] );
			$diffLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), $message['diff'], $query . $rcIdQuery, '', '', $aprops, $message['diff_tooltiptext'] );
		}

		# Make "last" link
		if ( $rc_last_oldid == 0 || $rc_type == RC_LOG || $rc_type == RC_MOVE || $rc_type == RC_MOVE_OVER_REDIRECT ) {
			$lastLink = $message['last'];
		} else {
			$lastLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), $message['last'],
			  $curIdEq.'&diff='.$rc_this_oldid.'&oldid='.$rc_last_oldid . $rcIdQuery, '', '', '', $message['last_tooltiptext'] );
		}

		# Make user link (or user contributions for unregistered users)
		if ( $rc_user == 0 ) {
			$contribsPage =& Title::makeTitle( NS_SPECIAL, 'Contributions' );
			$userLink = $this->skin->makeKnownLinkObj( $contribsPage,
				$rc_user_text, 'target=' . $rc_user_text );
		} else {
			$userPage =& Title::makeTitle( NS_USER, $rc_user_text );
			$userLink = $this->skin->makeLinkObj( $userPage, $rc_user_text );
		}
		if ( $watched && $baseRC->lastvisitedrevision )  {
			$lvrLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), $message['diff-to-lvr'],
			  "{$curIdEq}&diff=0&oldid={$baseRC->lastvisitedrevision}" , '', '', '', $message['diff-to-lvr_tooltiptext'] );
		} else {
			$lvrLink= '' ;
		}

		$rc->lvrlink  = $lvrLink;
		$rc->userlink = $userLink;
		$rc->lastlink = $lastLink;
		$rc->curlink  = $curLink;
		$rc->difflink = $diffLink;

		# Make user talk link
		$talkname = $wgContLang->getNsText( NS_TALK ); # use the shorter name
		$userTalkPage =& Title::makeTitle( NS_USER_TALK, $rc_user_text );
		$userTalkLink = $this->skin->makeLinkObj( $userTalkPage, $talkname );

		global $wgDisableAnonTalk;
		if ( ( $wgSysopUserBans || 0 == $rc_user ) && $wgUser->isAllowed('block') ) {
			$blockPage =& Title::makeTitle( NS_SPECIAL, 'Blockip' );
			$blockLink = $this->skin->makeKnownLinkObj( $blockPage,
				$message['blocklink'], 'ip='.$rc_user_text );
			if( $wgDisableAnonTalk )
				$rc->usertalklink = ' ('.$blockLink.')';
			else
				$rc->usertalklink = ' ('.$userTalkLink.' | '.$blockLink.')';
		} else {
			if( $wgDisableAnonTalk && ($rc_user == 0) )
				$rc->usertalklink = '';
			else
				$rc->usertalklink = ' ('.$userTalkLink.')';
		}

		# Put accumulated information into the cache, for later display
		# Page moves go on their own line
		$title = $rc->getTitle();
		$secureName = $title->getPrefixedDBkey();
		if ( $rc_type == RC_MOVE || $rc_type == RC_MOVE_OVER_REDIRECT ) {
			# Use an @ character to prevent collision with page names
			$this->rc_cache['@@' . ($this->rcMoveIndex++)] = array($rc);
		} else {
			if ( !isset ( $this->rc_cache[$secureName] ) ) $this->rc_cache[$secureName] = array() ;
			array_push ( $this->rc_cache[$secureName] , $rc ) ;
		}
		return $ret;
	}

}
?>
