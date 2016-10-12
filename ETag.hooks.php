<?php
/**
 * Hooks for ETag extension
 *
 * @file
 * @ingroup Extensions
 */


$wgHooks['ParserFirstCallInit'][] = 'ETag::onParserSetup';

class ETagHooks {
	// Register any render callbacks with the parser
	function onParserSetup( Parser $parser ) {
		// When the parser sees the <sample> tag, it executes renderTagSample (see below)
		$parser->setHook( 'etag', 'ETag::renderTagETag' );
	}

	// Render <etag>
	function renderTagETag( $input, array $args, Parser $parser, PPFrame $frame ) {
		// Nothing exciting here, just escape the user-provided input and throw it back out again (as example)
		return htmlspecialchars( $input );
	}
}



/* Parser::setHook() Create an HTML-style tag, e.g. <yourtag>special text</yourtag>.
   The callback should have the following form:
     function myParserHook( $text, $params, $parser, $frame ) { ... }
*/
