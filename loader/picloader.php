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
 
require_once ('../config/config.inc.php');
require_once ('../include/internal_config.inc.php');

$relpicpath = utf8_decode(str_replace('__phpAutoGallery__picLoader/', '', urldecode($HTTP_SERVER_VARS['REQUEST_URI'])));

if ($cfg['override_root_path']) {
        $fullpicpath = realpath(substr($cfg['override_root_path'], 0, -1) . $relpicpath);
}
else {
        $fullpicpath = realpath($HTTP_SERVER_VARS['DOCUMENT_ROOT'] . $relpicpath);
}

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