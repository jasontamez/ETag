<?php

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'ETag' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['ETag'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['ETagAlias'] = __DIR__ . '/ETag.i18n.alias.php';
	wfWarn(
		'Deprecated PHP entry point used for ETag extension. Please use wfLoadExtension ' .
		'instead, see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return true;
} else {
	die( 'This version of the ETag extension requires MediaWiki 1.25+' );
}
