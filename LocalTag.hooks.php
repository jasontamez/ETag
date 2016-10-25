<?php
/**
 * Hooks for LocalTag extension
 *
 * @file
 * @ingroup Extensions
 */

class LocalTagHooks {
	// Register any render callbacks with the parser
	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setHook( 'localtag', [ __CLASS__, 'renderTagLocalTag' ] );
	}

	// Render <localtag>
	public static function renderTagLocalTag( $input, array $args, Parser $parser, PPFrame $frame ) {
		// Load global with the user-defined attributes and settings.
		global $wgLocalTagSubstitutions, $wgLocalTagArgumentSeparator,
			$wgLocalTagArgumentMarker, $wgLocalTagVerboseBadAtts;
		// Load settings.
		// 	verboseBadAtts
		// 		: If true, insert a message to notify that an attribute wasn't found.
		// 	argumentSeparator
		// 		: Defaults to !.
		// 		: Separates arguments in an attribute's value.
		// 	argumentMarker
		// 		: Defaults to @@.
		// 		: Marks where arguments go in the administrator-defined attribute definitions.
		// 
		// EXAMPLES:
		// 
		// 	Wikitext:	<localtag example="justone">
		// 	Definition:	<li>@@</li>
		// 	Output: 	<li>justone</li>
		// 
		// 	Wikitext:	<localtag example="one!two!three">
		// 	Definition:	<b>@@</b> <i>@@</i> <b>@@</b>
		// 	Output: 	<b>one</b> <i>two</i> <b>three</b>
		// 	(NOTE: argument markers are counted starting with the PRE text, then the POST text)
		// 
		// INCORRECT VALUE HANDLING:
		// 
		// 	Wikitext:	<localtag example>
		// 	Definition:	<li>@@</li><li>@@</li>
		// 	Output: 	<li></li><li></li>
		// 	(No value == empty string)
		// 
		// 	Wikitext:	<localtag example="one!two">
		// 	Definition:	<li>@@</li><li>@@</li><li>@@</li>
		// 	Output: 	<li>one</li><li>two</li><li></li>
		// 	(Empty string for undefined extra definition values)
		// 
		// 	Wikitext:	<localtag example="one!two">
		// 	Definition:	<li>@@</li>
		// 	Output: 	<li>one</li>
		// 	(Extra arguments in the value are ignored)
		// 
		$verbose	= $wgLocalTagVerboseBadAtts;
		$sep		= $wgLocalTagArgumentSeparator;
		$mark		= $wgLocalTagArgumentMarker;
		// Variable for text/html going before the content.
		$pre = '';
		// Variable for text/html going after the content.
		$post = '';
		// Variable holding any/all CSS needed.
		$css = [];
		// Variable holding all valid, defined attributes referenced in the localtag.
		$subs = [];
		if( $args === [] ) {
			// No attributes were set! Nothing to do but throw an error, if desired.
			if( $verbose ) {
				$pre .= '[<strong>Error:</strong> &lt;localtag&gt; requires an/some attribute(s) to be effective]';
			}
		} else {
			// HTML is case insensitive. So, make user-defined attributes lowercase.
			$wgLocalTagSubstitutions = array_change_key_case( $wgLocalTagSubstitutions, CASE_LOWER );
			$args = array_change_key_case( $args, CASE_LOWER );
			foreach ( $args as $name => $value ) {
				// Check for administrator-defined attributes.
				// Multiple attributes are possible. They will be wrapped in order around the content.
				// Eg:  IN: <localtag foo bar baz>
				//     OUT: <foo><bar><baz>content</baz></bar></foo>
				if ( isset( $wgLocalTagSubstitutions[$name] ) ) {
					// 'attribute' => array( 'pre text', 'post text', 'css' );
					$code = $wgLocalTagSubstitutions[$name];
					$originalPRE = array_shift( $code ); // pre
					$originalPOST = array_shift( $code ); // post
					$originalCSS = array_shift( $code ); // css
					$givens = explode( $sep, $value ); // user-given arguments
					// Load the pre and post text, separated by $mark
					// Ignore '' pre/post text (since $pre and $post are already '')
					// $limit is used to mark the break between pre/post
					// (this catches a definition marker at the start or end of pre/post)
					$limit = uniqid();
					$needed = $originalPRE ? explode( $mark, $originalPRE ) : [];
					$needed[] = $limit;
					$needed = array_merge( $needed, ( $originalPOST ? explode( $mark, $originalPOST ) : [] ) );
					$FLAG = true;
					while ( $needed ) {
						$testcase = array_shift( $needed );
						if ( $testcase === $limit) {
							// We're done with pre text, switch to post text
							$FLAG = false;
						} else {
							if ( $FLAG === true ) {
								// We're in pre text
								$pre .= $testcase;
							} else {
								// We're in post text
								$post .= $testcase;
							}
							if ( $needed && $needed[0] !== $limit ) {
								// We're not at the end of the post text
								// && We're not at the end of the pre text
								// Therefore, we're at a break and need to insert an argument
								if ( $givens ) {
									// We have argument(s) for insertion
									if ( $FLAG ) {
										$pre .= array_shift( $givens );
									} else {
										$post .= array_shift( $givens );
									}
								}
							} // endif
						} // end if/else
					} // end while
					// Store this attribute in the list of substitutions used.
					$subs[] = $name;
					if ( $originalCSS ) {
						// Remove CSS from global, thus preventing multiple injections.
						$wgLocalTagSubstitutions[$name] = [ $originalPRE, $originalPOST, '' ];
						// Store CSS (if not blank)
						if( trim( $originalCSS ) ) {
							$css[] = $originalCSS;
						}
					}
				} elseif ( $verbose ) {
					// Attribute not found. Alert via a message.
					$pre .= "[<strong>Error:</strong> &lt;localtag $name&gt; not defined]";
				} // end isset() if
			} // end foreach loop
			if ( $css !== [] ) {
				// Send collected CSS with a comment denoting what it's for.
				array_unshift( $css,
						'<style type="text/css">',
						'/* CSS for LocalTag: '.implode( ' ', $subs ).' */' );
				$CSS = implode( "\n\t", $css )."\n</style>\n";
				$parser->getOutput()->addHeadItem( $CSS );
			} // end $css if
		} // end $args if
		// Parse content inside <localtag></localtag> as wikitext.
		$output = $parser->recursiveTagParse( $input, $frame );
		return $pre.$output.$post;
	} // end render function
}

