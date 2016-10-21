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
		global $wgLocalTagSubstitutions,$wgLocalTagSettings;

		// Get output page object
		$out = $this->getOutput();

		// Set title of the page
		$out->setPageTitle( $this->msg( 'localtag-special-title' ) );

		// Add help link??
// 		$out->addHelpLink( 'How to become a MediaWiki hacker' );

		// Print introductory text.
		$out->addWikiMsg( 'localtag-special-intro' );

		// Get various words that may need translating.
		$att =	$this->msg( 'localtag-attribute' );
		$atts =	$this->msg( 'localtag-attributes' );
		$val =	$this->msg( 'localtag-value' );
		$vals =	$this->msg( 'localtag-values' );
		$arg =	$this->msg( 'localtag-argument' );
		$args =	$this->msg( 'localtag-arguments' );
		$ar =	$this->msg( 'localtag-arg' );
		$doc =	$this->msg( 'localtag-documentation' );
		$wikt =	$this->msg( 'localtag-wikitext' );
// 		$x =	$this->msg( 'localtag-' );

		// Gather settings
		$subs =	$wgLocalTagSubstitutions;
		$cnt =	count( $subs );
		$sep =	isset( $wgLocalTagSettings['argumentSeparator'] ) ? $wgLocalTagSettings['argumentSeparator'] : '!';
		$mark =	isset( $wgLocalTagSettings['argumentMarker'] ) ? $wgLocalTagSettings['argumentMarker'] : '@@';
		$html =	isset( $wgLocalTagSettings['showHTMLOnSpecialPage'] ) ? $wgLocalTagSettings['showHTMLOnSpecialPage'] : false;
		$css =	isset( $wgLocalTagSettings['showCSSOnSpecialPage'] ) ? $wgLocalTagSettings['showCSSOnSpecialPage'] : false;

		// Initialize variables we need to determine
		$VALS =	false; // Are values asked for?
		$ARGS =	false; // Are multiple arguments asked for?   SUBSTR_COUNT

		// Build table
		$table = '{| class="wikitable tableC"'."\n|-\n!".$att."\n!".ucfirst( $doc )."\n";
		if( $html ) {
			$table .= "!HTML\n";
		}
		if( $css ) {
			$table .= "!CSS\n";
		}
		foreach ( $subs as $attribute => $definitions ) {
			$table .= "|-\n";
			$d = $definitions[3];
			$h = $definitions[0];
			$i = $definitions[1];
			$a = substr_count( $h, $mark ) + substr_count( $i, $mark );
			$arglist = '';
			if ( $a > 0 ) {
				$VALS = true;
				if ( $a > 1 ) {
					$ARGS = true;
					$arrrglist = [];
					while ( $a > 0 ) {
						array_unshift( $arrrglist, $ar.$a-- );
					}
					$x = implode( $sep, $arrrglist );
				} else {
					$x = $val;
					$arrrglist = [ $val ];
				}
				$arglist = "=".$x;
			}
			$table .= "|<nowiki>".$attribute.$arglist"</nowiki>\n|".$d."\n";
			if ( $html ) {
				$z = array_merge( array_push( explode( $mark, $h ), $wikt ), explode( $mark, $i ) );
				$x = '';
				while ( $z ) {
					$y = array_shift( $z );
					if ( $z && $y !== $wikt && $z[0] !== $wikt ) {
						$x .= htmlspecialchars( $y ).array_pop( $arrrglist );
					} elseif ( $y !== $wikt ) {
						$x .= htmlspecialchars( $y );
					}
				}
				$table .= "|<nowiki>".$x."</nowiki>\n";
			}
			if ( $css ) {
				$table .= "|<nowiki>".$definitions[2]."</nowiki>\n";
			}
		} // end foreach
		$table .= "|}\n";
		$out->addWikiText($table);
	}
}
