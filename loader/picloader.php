<?php
/**
 * picloader.php -- wrapper for original image files
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

$relpicpath = utf8_decode(str_replace('__phpAutoGallery__picLoader/', '', urldecode($HTTP_SERVER_VARS['REQUEST_URI'])));

$fullpicpath = str_replace($HTTP_SERVER_VARS['SCRIPT_NAME'], "", $HTTP_SERVER_VARS['SCRIPT_FILENAME']) . $relpicpath;

$info = getimagesize($fullpicpath);
if ($info[2] == 1) {
	$mime = 'image/gif';
}
elseif ($info[2] == 2) {
	$mime = 'image/jpeg';
}
elseif ($info[2] == 3) {
	$mime = 'image/png';
}

$filedate = gmdate("D, d M Y H:i:s", filemtime($fullpicpath)) . ' GMT';

$headers = getallheaders();

if ($headers['If-Modified-Since'] == $filedate) {
	header('HTTP/1.1 304 Not Modified');
	exit;
}
else {
	header('Content-type: image/jpeg');
	header('Last-Modified: ' . $filedate);
	header('Content-type: ' . $mime);
	readfile($fullpicpath);
}
?>