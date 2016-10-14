<?php
/**
 * Hooks for ExtraTag extension
 *
 * @file
 * @ingroup Extensions
 */


class ExtraTagHooks {
	// Register any render callbacks with the parser
	public static function onParserFirstCallInit( Parser $parser ) {
		// When the parser sees the <sample> tag, it executes renderTagSample (see below)
		$parser->setHook( 'extratag', array(__CLASS__, 'renderTagExtraTag') );
		$parser->setHook( 'extag', array(__CLASS__, 'renderTagExtraTag') );
		$parser->setHook( 'etag', array(__CLASS__, 'renderTagExtraTag') );
	}

	// Render <etag>
	public static function renderTagExtraTag( $input, array $args, Parser $parser, PPFrame $frame ) {
		// Nothing exciting here, just escape the user-provided input and throw it back out again (as example)
		global $wgExtraTagSubstitutions;
		$pre = '';
		$post = '';
		foreach ($args as $name => $value) {
		  // Check user-defined variables.
		  // Currently, this will only use the LAST found argument, if multiple are given.
		  // To-do: - Is there any way to utilize any/all arguments?
		  //        - Is there any reason to pass the arguments' values?
		  if(isset($wgExtraTagSubstitutions[$name])) {
		    // 'argument' => array('pre text', 'post text');
		    $code = $wgExtraTagSubstitutions[$name];
		    $pre = array_shift($code);
		    $post = array_shift($code);
		  }
		}
		// Parse wikitext inside <extratag></extratag>
		$output = $parser->recursiveTagParse( $input, $frame );
		return $pre.$output.$post;
	}
}

