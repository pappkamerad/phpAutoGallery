<?php
/**
 * config.inc.php -- configuration file for phpAutoGallery
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
 
// cache path (IMPORTANT)
// phpAutoGallery will use this folder for:
// - thumbnails
// - resized images
// - smarty compile directory
// - other infos...
// folder must exist and be writeable by webserver-user!
$cfg['tmp_path'] = '/var/tmp/';
// example for windows:
//$cfg['tmp_path'] = 'c:/temp/';

// which template/skin to use
$cfg['which_template'] = 'decoded_green';

// the name of your gallery
// shown as prefix for the html title
$cfg['gallery_name'] = 'phpAutoGallery';

// shown as the name of the root gallery folder
$cfg['root_folder_name'] = 'phpAutoGallery';

// thumbnails size. sets the minimum width/height. corresponding
// width and height values calculation is depending on picture bias)
$cfg['thumb_size'] = 75;

// next/previous-thumbnails size. sets the minimum width/height. corresponding
// width and height values calculation is depending on picture bias)
$cfg['next_previous_size'] = 75;

// sizes of pictures (picturewidth values)
// You can add any size you like by adding a new value in the
// $cfg['view_sizes'] array.
// $cfg['view_sizes'][<new index>] = <new width value>
$cfg['view_sizes'][0] = 640;
$cfg['view_sizes'][1] = 800;
$cfg['view_sizes'][2] = 1024;

// default size for picture views (array index from $cfg['view_sizes'])
$cfg['default_view'] = 0;

// allow the viewer to see the original picture (original size)
$cfg['allow_original_view'] = true;

// jpeg quality of the resized images
$cfg['jpeg_quality'] = 75;

// logo/copyright image
// just enter the filename of the logo image
// and put the logo file in the __phpAutoGallery/img folder
// leave empty for no logo/copyright
// for transparency effects please use 24-bit png files
$cfg['logo_image'] = '';
$cfg['logo_position_x'] = 'left +10';
$cfg['logo_position_y'] = 'bottom -10';

// thumbnail resize method. (does not effect the fullsize image
// resize method)
// "resample" -> high quality but slow algorithmus (GD >= 2.0.1)
// "resize" -> ugly but faster
$cfg['thumbnail_resize_method'] = 'resample';

// the number of pictures (thumbnails) that will be shown on a single
// directory listing page.
$cfg['pics_per_page'] = 10;

// specify how the directories and files get sorted
// possible values: 'name', 'date' or 'size'
$cfg['sort_value'] = 'name';
// specifiy the sort order
// possible values: 'ASC' (for ascending) and
// 'DESC' (for descending)
$cfg['sort_order'] = 'ASC';

// You can specify the format of date/time-displays here
// it uses strftime-syntax
// information about the syntax:
// http://www.php.net/manual/en/function.strftime.php
$cfg['timeformat'] = "%Y/%m/%d - %H:%M";
// region setting for timedisplay.
// leave empty for system default value.
// information about possible values:
// http://www.php.net/manual/en/function.setlocale.php
//$cfg['locale'] = 'de_DE';

// set the filename suffix for description files
// (i.e. 'desc' > description in: 'mypicture.desc')
$cfg['description_extension'] = 'desc';
// set the filename prefix for folder description files
// (suffix will be $cfg['description_extension'])
// (i.e. '_folder' > description in: '_folder.desc')
$cfg['folder_description_name'] = '_folder';

// username and password for the administration tool
$cfg['admin']['username'] = 'admin';
// CHANGE the this password!
$cfg['admin']['password'] = 'pwd';

// hide specific foldernames / filenames (case sensitiv)
$cfg['hide_folder'] = array(
	'__phpAutoGallery',
	'CVSROOT'
);
$cfg['hide_file'] = array(
	'.htaccess',
	'htaccess-dist'
);

// if you have special characters like german umlaute not displayed correctly
// and you are using samba 2.x to upload your pictures, then you can try to
// enable this setting.
// phpAutoGallery then tries to convert the default samba "cp850"-charset to
// the "ISO-8859-2"-charset.
$cfg['samba_2_charset_workaround'] = false;
?>