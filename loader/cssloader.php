<?php
/**
 * cssloader.php -- wrapper for css files
 *
 * Copyright (C) 2003 Martin Theimer
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Contact: Martin Theimer <pappkamerad@decoded.net>
 *
 * The latest version of phpAutoGallery can be obtained from:
 * http://sourceforge.net/projects/phpautogallery
 *
 * $Id$
 */
 
$relpicpath = str_replace('__phpAutoGallery__cssLoader/', '', urldecode($HTTP_SERVER_VARS['REQUEST_URI']));
$cssfile = substr($relpicpath, strrpos($relpicpath, '/'));
$relpicpath = substr($relpicpath, 0, strrpos($relpicpath, '/')) . '/__phpAutoGallery/css' . $cssfile;

$fullpicpath = str_replace($HTTP_SERVER_VARS['SCRIPT_NAME'], "", $HTTP_SERVER_VARS['SCRIPT_FILENAME']) . $relpicpath;

$filedate = gmdate("D, d M Y H:i:s", filemtime($fullpicpath)) . ' GMT';

$headers = getallheaders();

if ($headers['If-Modified-Since'] == $filedate) {
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