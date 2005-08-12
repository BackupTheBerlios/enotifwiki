<?php
/**
 *
 * @package MediaWiki
 * @subpackage SpecialPage
 */

/**
 * constructor
 */
function wfSpecialUserlogout() {
	global $wgUser, $wgOut, $returnto;
	global $wgAutoLogin;

	if (wfRunHooks('UserLogout', array(&$wgUser))) {

		if (!$wgAutoLogin) {
			$wgUser->logout();

			wfRunHooks('UserLogoutComplete', array(&$wgUser));
		
			$wgOut->mCookies = array();
			$wgOut->setRobotpolicy( 'noindex,nofollow' );
			$wgOut->addHTML( wfMsg( 'logouttext' ) );
		} else	$wgOut->addHTML( wfMsg( 'disabled_on_this_wiki' ) );

		$wgOut->returnToMain();
		
	}
}

?>
