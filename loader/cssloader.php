<?php
/**
 * cssloader.php -- wrapper for css files
 *
 * Copyright (C) 2003, 2004 Martin Theimer
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Contact: Martin Theimer <pappkamerad@decoded.net>
 *
 * The latest version of phpAutoGallery can be obtained from:
 * http://sourceforge.net/projects/phpautogallery
 *
 * $Id$
 */
 
$relpicpath = str_replace('__phpAutoGallery__cssLoader/', '__phpAutoGallery/', urldecode($HTTP_SERVER_VARS['REQUEST_URI']));

if (isset($HTTP_SERVER_VARS['SCRIPT_URL']) && $HTTP_SERVER_VARS['SCRIPT_URL'] != $HTTP_SERVER_VARS['SCRIPT_NAME']) {
	// CGI
	$fullpicpath = str_replace($HTTP_SERVER_VARS['SCRIPT_URL'], "", $HTTP_SERVER_VARS['SCRIPT_FILENAME']) . $relpicpath;
}
else {
	// APACHE
	$fullpicpath = str_replace($HTTP_SERVER_VARS['SCRIPT_NAME'], "", $HTTP_SERVER_VARS['SCRIPT_FILENAME']) . $relpicpath;
}

$filedate = gmdate("D, d M Y H:i:s", filemtime($fullpicpath)) . ' GMT';

if ($HTTP_SERVER_VARS['HTTP_IF_MODIFIED_SINCE'] == $filedate) {
	header('HTTP/1.1 304 Not Modified');
	exit;
}
else {
	header('Last-Modified: ' . $filedate);
	header('Content-Length: ' . filesize($fullpicpath));
	header('Content-type: text/css');
	readfile($fullpicpath);
}
?>