<?php

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'ExtraTag' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['ExtraTag'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['ExtraTagAlias'] = __DIR__ . '/ETag.i18n.alias.php';
	wfWarn(
		'Deprecated PHP entry point used for ExtraTag extension. Please use wfLoadExtension ' .
		'instead, see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return true;
} else {
	die( 'This version of the ExtraTag extension requires MediaWiki 1.25+' );
}
