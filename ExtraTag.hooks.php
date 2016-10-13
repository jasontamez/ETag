<?php
/**
 * Hooks for ExtraTag extension
 *
 * @file
 * @ingroup Extensions
 */


$wgHooks['ParserFirstCallInit'][] = 'ExtraTag::onParserSetup';

class ExtraTagHooks {
	// Register any render callbacks with the parser
	function onParserSetup( Parser $parser ) {
		// When the parser sees the <sample> tag, it executes renderTagSample (see below)
		$parser->setHook( 'extratag', 'ExtraTag::renderTagExtraTag' );
		$parser->setHook( 'etag', 'ExtraTag::renderTagExtraTag' );
	}

	// Render <etag>
	function renderTagExtraTag( $input, array $args, Parser $parser, PPFrame $frame ) {
		// Nothing exciting here, just escape the user-provided input and throw it back out again (as example)
		return htmlspecialchars( $input );
	}
}



/* Parser::setHook() Create an HTML-style tag, e.g. <yourtag>special text</yourtag>.
   The callback should have the following form:
     function myParserHook( $text, $params, $parser, $frame ) { ... }
*/
