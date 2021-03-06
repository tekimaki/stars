<?php
/**
* $Header$
* date created 2006/02/10
* @author xing <xing@synapse.plus.com>
* @version $Revision$
* @package stars
*/

/**
 * Setup
 */
require_once( "../kernel/setup_inc.php" );
$gBitSystem->verifyPackage( 'stars' );
$starsfeed = array();

if( @BitBase::verifyId( $_REQUEST['content_id'] ) && @BitBase::verifyId( $_REQUEST['stars_rating'] ) ) {
	if( $tmpObject = LibertyBase::getLibertyObject( $_REQUEST['content_id'] ) ) {
		// check if this feature allows rating
		if( $gBitSystem->isFeatureActive( 'stars_rate_'.$tmpObject->getContentType() ) ) {
			$starsfeed = array();
			$stars = new LibertyStars( $tmpObject->mContentId );

			if( !$gBitUser->isRegistered() ) {
				$starsfeed['error'] = tra( "You need to log in to rate." );
			} else {
				if( $stars->store( $_REQUEST ) ) {
					//$starsfeed['success'] = tra( "Thank you for rating." );
				} else {
					$starsfeed['error'] = $stars->mErrors;
				}
			}
		}
	}
	// get up to date reading
	$stars->load();
	$serviceHash = array_merge( $tmpObject->mInfo, $stars->mInfo, $stars->getUserRating( $tmpObject->mContentId ) );
	$gBitSmarty->assign( 'serviceHash', $serviceHash );
} else {
	$starsfeed['warning'] = tra( "There was a problem trying to apply your rating" );
}
$gBitSmarty->assign( "starsfeed", $starsfeed );

if( $gBitThemes->isAjaxRequest() ) {
	if( !empty( $_REQUEST['type'] ) ) {
		$gBitSmarty->assign( 'type', $_REQUEST['type'] );
	}
	print $gBitSmarty->fetch( 'bitpackage:stars/stars_inline_service.tpl' );
} elseif( !empty( $tmpObject ) && method_exists( $tmpObject, 'getDisplayUrl' )) {
	bit_redirect( $tmpObject->getDisplayUrl() );
}
?>
