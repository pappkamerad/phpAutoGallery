<?php
/**
 * config.inc.php -- configuration file for phpAutoGallery
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
 
// cache path. phpAutoGallery will put its resized images here
// must be writeable by webserver-user!
$cfg['tmp_path'] = '/var/tmp/';

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
$cfg['pics_per_page'] = 30;

// filennames of the different icons used buy phpAutoGallery
$cfg['icon_folder'] = 'folder.png';
$cfg['icon_video_mpg'] = 'video.png';
$cfg['icon_video_mov'] = 'video.png';
$cfg['icon_video_asf'] = 'video.png';
$cfg['icon_video_avi'] = 'video.png';
$cfg['icon_video_wmv'] = 'video.png';

// hide specific foldernames / filenames (case sensitiv)
$cfg['hide_folder'] = array(
	'__phpAutoGallery'
);
$cfg['hide_file'] = array(
	'htaccess-dist'
);

?>