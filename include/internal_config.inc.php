<?php
/**
 * internal_config.inc.php -- internal configurations for phpAutoGallery
 *
 * do not change this!
 *
 * Copyright (C) 2003 Martin Theimer
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Contact: Martin Theimer <pappkamerad@decoded.net>
 *
 * The latest version of phpAutoGallery can be obtained from:
 * http://www.decoded.net/projects/phpAutoGallery
 *
 * $Id$
 */
$cfg['version'] = '0.9.0';
$cfg['copyright'] = 'Copyright &copy; 2003&nbsp;<a href="mailto:pappkamerad@decoded.net">Martin Theimer</a>';

$cfg['wrapper_path'] = '__phpAutoGallery/wrapper.php';
$cfg['tmp_pAG_path'] = 'phpAutoGallery/';

$cfg['types']['jpg'] = 1;
$cfg['types']['jpeg'] = 1;
$cfg['types']['gif'] = 1;
$cfg['types']['png'] = 1;

$cfg['types']['mov'] = 2;
$cfg['types']['avi'] = 2;
$cfg['types']['asf'] = 2;
$cfg['types']['wmv'] = 2;
$cfg['types']['mpeg'] = 2;
$cfg['types']['mpg'] = 2;

$cfg['types']['txt'] = 0;

$cfg['mime_types']['avi'] = 'video/x-msvideo';
$cfg['mime_types']['asf'] = 'video/x-msvideo';
$cfg['mime_types']['mov'] = 'video/quicktime';
$cfg['mime_types']['wmv'] = 'video/x-ms-wmv';
$cfg['mime_types']['mpeg'] = 'video/mpeg';
$cfg['mime_types']['mpg'] = 'video/mpeg';
$cfg['mime_types']['mpe'] = 'video/mpeg';
?>