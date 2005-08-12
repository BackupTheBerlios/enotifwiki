<?
/* The ImageLink extension allows to define a link for an image:
 *
 *<imagelink>mediawiki.png|http://www.mediawiki.org/|MediaWiki</imagelink>
 * Adapted from http://leuksman.com/extensions/ImageLink.phps
 *
 * Further reading:
 * http://meta.wikimedia.org/wiki/Write_your_own_MediaWiki_extension
 */

$wgExtensionFunctions[] = "wfSetupImageLink";

function wfSetupImageLink() {
	global $wgParser;
 	$wgParser->setHook( 'imagelink', 'imageLinkHandler' );
}

function imageLinkHandler( $data ) {

	$bits = array_map( 'trim', explode( '|', $data ) );
	$nbits = count( $bits );
	if( $nbits < 1 ) return "(invalid image link)";
	else $imageName = $bits[0];

	if( $nbits < 2 ) $linkTarget = 'Image:' . $imageName;
	else $linkTarget = $bits[1];

	if( $nbits < 3 ) $altText = $linkTarget;
	else $altText = $bits[2];

	return formatImageLink( $imageName, $linkTarget, $altText );
}

function formatImageLink( $imageName, $linkTarget, $altText ) {
	$imageTitle = Title::makeTitleSafe( NS_IMAGE, $imageName );
	if( is_null( $imageTitle ) ) return "(invalid image name)";

	$image = Image::newFromTitle( $imageTitle );
	if( is_null( $image ) ) return "(invalid image)";

	if( preg_match( '/^(' . URL_PROTOCOLS . ')/', $linkTarget ) ) {
	$linkUrl = $linkTarget;

	} else {
		$linkTitle = Title::newFromText( $linkTarget );
		if( is_null( $linkTitle ) ) return "(invalid link target)";
		$linkUrl = $linkTitle->getLocalUrl();
	}

	$imageUrl = $image->getViewURL();
	return '<a href="' .
	htmlspecialchars( $linkUrl ) .
	'"><img src="' .
	htmlspecialchars( $image->getViewURL() ) .
	'" width="' .
	IntVal( $image->getWidth() ) .
	'" height="' .
	IntVal( $image->getheight() ) .
	'" alt="' .
	htmlspecialchars( $altText ) .
	'" title="' .
	htmlspecialchars( $altText ) .
	'" /></a>';
}

?>

