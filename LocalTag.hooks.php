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
		global $wgLocalTagSubstitutions;
		$pre = '';
		$post = '';
		foreach ($args as $name => $value) {
		  // Check user-defined variables.
		  // Currently, this will only use the LAST found argument, if multiple are given.
		  // To-do: - Is there any way to utilize any/all arguments?
		  //        - Is there any reason to pass the arguments' values?
		  if(isset($wgLocalTagSubstitutions[$name])) {
		  	// 'argument' => array('pre text', 'post text', 'css');
		  	$code = $wgLocalTagSubstitutions[$name];
		  	$pre = array_shift($code);
		  	$post = array_shift($code);
		  	$css = array_shift($code);
		  	if($css) {
		  		$parser->getOutput()->addHeadItem('<style type="text/css">'.$css.'</style>');
			  	// Remove CSS from global, thus preventing multiple injections.
			  	$wgLocalTagSubstitutions[$name] = array($pre, $post, '');
		  	}
		  }
		}
		// Parse wikitext inside <localtag></localtag>
		$output = $parser->recursiveTagParse( $input, $frame );
		return $pre.$output.$post;
	}
}

