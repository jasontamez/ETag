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
		global $wgLocalTagSubstitutions,$wgLocalTagSettings;
		// Load settings.
		$verbose = isset( $wgLocalTagSettings['verboseBadArgs'] ) ? $wgLocalTagSettings['verboseBadArgs'] : false ;
		$sep = isset( $wgLocalTagSettings['argumentSeparator'] ) ? $wgLocalTagSettings['argumentSeparator'] : '!';
		$mark = isset( $wgLocalTagSettings['argumentMarker'] ) ? $wgLocalTagSettings['argumentMarker'] : '@@';
		// Variable for text going before the content.
		$pre = '';
		// Variable for text going after the content.
		$post = '';
		// Variable holding any/all CSS needed.
		$css = [];
		// Variable holding all valid, defined attributes referenced in the localtag.
		$subs = [];
		foreach ( $args as $name => $value ) {
		  // Check for user-defined attributes.
		  // Multiple attributes are possible. They will be wrapped in order around the content.
		  // Eg:  IN: <localtag foo bar baz>
		  //     OUT: <foo><bar><baz>content</baz></bar></foo>
		  // To-do: - Is there any way to utilize any/all arguments?
		  //        - Is there any reason to pass the arguments' values?
		  //        - What do we do when an attribute fails?
		  if ( isset( $wgLocalTagSubstitutions[$name] ) ) {
		  	// 'attribute' => array( 'pre text', 'post text', 'css' );
		  	$code = $wgLocalTagSubstitutions[$name];
		  	$foo = array_shift( $code ); // pre
		  	$bar = array_shift( $code ); // post
		  	$baz = array_shift( $code ); // css
		  	$pre .= $foo;
		  	$post = $bar.$post;
		  	$subs[] = $name;
		  	if ( $baz ) {
			  	// Remove CSS from global, thus preventing multiple injections.
			  	$wgLocalTagSubstitutions[$name] = [ $foo, $bar, '' ];
			  	// Store CSS
			  	$css[] = $baz;
		  	}
		  }
		}
		if ( $css !== [] ) {
			// Send collected CSS with a comment denoting what it's for.
			array_unshift( $css,
					'<style type="text/css">',
					'/* CSS for LocalTag: '.implode( ' ', $subs ).' */' );
			$CSS = implode( "\n\t", $css )."\n</style>\n";
			$parser->getOutput()->addHeadItem( $CSS );
		}
		// Parse content inside <localtag></localtag> as wikitext.
		$output = $parser->recursiveTagParse( $input, $frame );
		return $pre.$output.$post;
	}
}

