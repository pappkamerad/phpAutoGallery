<?php
/**
 * picloader.php -- wrapper for resized image files
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

$relpicpath = str_replace('__phpAutoGallery__picLoaderTmp/', '', strstr(urldecode($HTTP_SERVER_VARS['REQUEST_URI']), '__phpAutoGallery__picLoaderTmp/'));
$fullpicpath = $cfg['tmp_path'] . $cfg['tmp_pAG_path'] . $relpicpath;

$filedate = gmdate("D, d M Y H:i:s", filemtime($fullpicpath)) . ' GMT';

$headers = getallheaders();

if ($headers['If-Modified-Since'] == $filedate) {
	header('HTTP/1.1 304 Not Modified');
	exit;
}
else {
	header('Content-type: image/jpeg');
	header('Last-Modified: ' . $filedate);
	header('Content-Length: ' . filesize($fullpicpath));
	readfile($fullpicpath);
}
?>