<?php
/**
 * videoloader.php -- wrapper for video files
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
 
$relpicpath = utf8_decode(str_replace('__phpAutoGallery__videoLoader/', '', urldecode($HTTP_SERVER_VARS['REQUEST_URI'])));

$fullpicpath = str_replace($HTTP_SERVER_VARS['SCRIPT_NAME'], "", $HTTP_SERVER_VARS['SCRIPT_FILENAME']) . $relpicpath;

$ext = strtolower(substr($relpicpath, strrpos($relpicpath, '.') +1));

$mime = $cfg['mime_types'][$ext];

$filedate = gmdate("D, d M Y H:i:s", filemtime($fullpicpath)) . ' GMT';

if ($HTTP_SERVER_VARS['HTTP_IF_MODIFIED_SINCE'] == $filedate) {
	header('HTTP/1.1 304 Not Modified');
	exit;
}
else {
	header('Last-Modified: ' . $filedate);
	header('Content-Length: ' . filesize($fullpicpath));
	header('Content-type: ' . $mime);
	readfile($fullpicpath);
}
?>