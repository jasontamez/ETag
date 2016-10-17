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
		$parser->setHook( 'localtag', array(__CLASS__, 'renderTagLocalTag') );
	}

	// Render <localtag>
	public static function renderTagLocalTag( $input, array $args, Parser $parser, PPFrame $frame ) {
		// Load global with the user-defined definitions.
		global $wgLocalTagSubstitutions;
		// Variable for text going before the content.
		$pre = '';
		// Variable for text going after the content.
		$post = '';
		// Variable holding any/all CSS needed.
		$css = array();
		// Variable holding all valid, defined arguments referenced in the localtag.
		$subs = array();
		foreach ($args as $name => $value) {
		  // Check for user-defined definitions.
		  // Multiple arguments are possible. They will be wrapped in order around the content.
		  // Eg:  IN: <localtag foo bar baz>
		  //     OUT: <foo><bar><baz>content</baz></bar></foo>
		  // To-do: - Is there any way to utilize any/all arguments?
		  //        - Is there any reason to pass the arguments' values?
		  if(isset($wgLocalTagSubstitutions[$name])) {
		  	// 'argument' => array('pre text', 'post text', 'css');
		  	$code = $wgLocalTagSubstitutions[$name];
		  	$foo = array_shift($code); // pre
		  	$bar = array_shift($code); // post
		  	$baz = array_shift($code); // css
		  	$pre .= $foo;
		  	$post = $bar.$post;
		  	$subs[] = $name;
		  	if($baz) {
			  	// Remove CSS from global, thus preventing multiple injections.
			  	$wgLocalTagSubstitutions[$name] = array($foo, $bar, '');
			  	// Store CSS
			  	$css[] = $baz;
		  	}
		  }
		}
		if($css !== array()) {
			// Send collected CSS with a comment denoting what it's for.
			array_unshift($css, '<style type="text/css">', '/* CSS for LocalTag: '.implode(' ', $subs).' */');
			$CSS = implode("\n\t", $css)."\n</style>\n";
			$parser->getOutput()->addHeadItem($CSS);
		}
		// Parse content inside <localtag></localtag> as wikitext.
		$output = $parser->recursiveTagParse( $input, $frame );
		return $pre.$output.$post;
	}
}

