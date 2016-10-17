<?php

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'LocalTag' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['LocalTag'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['LocalTagAlias'] = __DIR__ . '/LocalTag.i18n.alias.php';
	wfWarn(
		'Deprecated PHP entry point used for LocalTag extension. Please use wfLoadExtension ' .
		'instead, see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return true;
} else {
	die( 'This version of the LocalTag extension requires MediaWiki 1.25+' );
}
