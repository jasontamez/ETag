<?php
/**
 * HelloWorld SpecialPage for LocalTag extension
 *
 * @file
 * @ingroup Extensions
 */

class SpecialLocalTag extends SpecialPage {
	public function __construct() {
		parent::__construct( 'LocalTag' );
	}

	/**
	 * Show the page to the user
	 *
	 * @param string $sub The subpage string argument (if any).
	 *  [[Special:LocalTagList/subpage]].
	 */
	public function execute( $sub ) {
		$out = $this->getOutput();

		$out->setPageTitle( $this->msg( 'boilerplate-helloworld' ) );

		$out->addHelpLink( 'How to become a MediaWiki hacker' );

// 		$out->addWikiMsg( 'boilerplate-helloworld-intro' );

	}
}
