<?php
/**
 * wrapper.php -- main script for phpAutoGallery
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
 
require_once ('./include/functions.inc.php');
require_once ('./include/Image_Toolbox.class.php');
require_once ('./include/internal_config.inc.php');
require_once ('./config/config.inc.php');
require_once ('./smarty/Smarty.class.php');

$pt_start = getmicrotime();

// standard HTTP header
header('Content-Type: text/html; charset=utf-8');

// no script timeout. (thumb generation may take some time)
set_time_limit(0);

$template = new Smarty;
$template->template_dir = './templates/'; 
$template->compile_dir = './smarty/templates_c/';

if ($cfg['override_root_path']) {
        $filesystem_root_path = $cfg['override_root_path'];
}
else {
        $filesystem_root_path = realpath($HTTP_SERVER_VARS['DOCUMENT_ROOT']) . '/';
}
$filesystem_pAG_path_abs = str_replace($cfg['wrapper_path'], '', realpath($HTTP_SERVER_VARS['SCRIPT_FILENAME']));
$filesystem_pAG_path_rel = '/' . str_replace($filesystem_root_path, '', $filesystem_pAG_path_abs);
$web_pAG_path_abs = $HTTP_SERVER_VARS['SERVER_NAME'] . $filesystem_pAG_path_rel;
$web_pAG_path_rel = $filesystem_pAG_path_rel;

if ($HTTP_SERVER_VARS['REDIRECT_URL'] . '/' === $web_pAG_path_rel) {
	// special root dir without trailing slash
	$url_request_part = '';
}
elseif ($web_pAG_path_rel == '/') {
	$url_request_part = substr($HTTP_SERVER_VARS['REDIRECT_URL'], 1);
}
else {
	$url_request_part = str_replace($web_pAG_path_rel, '', $HTTP_SERVER_VARS['REDIRECT_URL']);
}

$template->assign('vCurrentRequest', '/' . $url_request_part);
$template->assign('vRootPath', $web_pAG_path_rel);
$template->assign('vVersion', $cfg['version']);
$template->assign('vCopyright', $cfg['copyright']);
$template->assign('vGalleryName', $cfg['gallery_name']);

if (!file_exists($filesystem_pAG_path_abs . $url_request_part)) {
	$wrongfile = $web_pAG_path_rel . $url_request_part;
	$template->assign('vNotFoundURL', $wrongfile);
	$template->assign('internContentTemplate', 'notfound.tpl');
} 
else {
	if (!is_file($filesystem_pAG_path_abs . $url_request_part)) {
		// directory list mode
		
		// where am i?
		if ((substr($url_request_part, -1, 1) != '/') && ($url_request_part != '')) {
			$url_request_part .= '/';
		}
		$filesystem_current_path = $filesystem_pAG_path_abs . $url_request_part;
		$web_current_path = $url_request_part;

		// get navigation path array
		$nav_dummy = explode('/', substr($web_current_path, 0, -1));
		if ($url_request_part == '') {
			$current_nav[0]['href'] = '';
		}
		else {
			$current_nav[0]['href'] = $web_pAG_path_rel;
		}
		$current_nav[0]['name'] = $cfg['root_folder_name'];
		$current_nav_current['href'] = '';
		$current_dummy_name = array_pop($nav_dummy);
		for ($i = 0; isset($nav_dummy[$i]); $i++) {
			$current_nav[($i + 1)]['name'] = $nav_dummy[$i];
			$current_nav[($i + 1)]['href'] = $web_pAG_path_rel;
			for ($u = 0; $u <= $i; $u++) {
				$current_nav[($i + 1)]['href'] .= $nav_dummy[$u] . '/';
			}
		}
		if ($url_request_part != '') {
			$current_nav[($i + 1)]['href'] = '';
			$current_nav[($i + 1)]['name'] = $current_dummy_name;
		}
		if (!$current_dummy_name) {
			$current_dir_name = $cfg['root_folder_name'];
		}
		else {
			$current_dir_name = $current_dummy_name;
		}
		
		// get current directory's files and subdirectories
		$current_files = getDirFiles($filesystem_current_path);
		$current_dirs = getDirDirs($filesystem_current_path);
		$current_dir_bytecount = getDirBytes($filesystem_current_path);
		$current_dir_bytecounttotal = getDirBytesTotal($filesystem_current_path);
		
		$current_dir_dirs = array();
		if (isset($current_dirs[0])) {
			$i = 0;
			foreach ($current_dirs as $dir) {
				$current_dir_dirs[$i]['href'] = $web_pAG_path_rel . $url_request_part . $dir;
				$current_dir_dirs[$i]['name'] = $dir;
				$current_dir_dirs[$i]['img'] = $web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/' . $cfg['icon_folder'];
				$i++;
			}
			$current_dir_dircount = sizeof($current_dir_dirs);
		}
		else {
			$current_dir_dirs = 0;
			$current_dir_dircount = 0;
		}
		
		$current_dir_files = array();
		$current_dir_filecount[0] = 0;
		$current_dir_filecount[1] = 0;
		$current_dir_filecount[2] = 0;
		$current_dir_files_highest = 0;
		$current_dir_files_widest = 0;
		if (isset($current_files[0])) { 
			$i = 0;
			$u = 0;
			if (!isset($_GET['offset'])) {
				$per_page_start = 0;
			}
			else {
				$per_page_start = $_GET['offset'];
			}
			$per_page_end = $per_page_start + $cfg['pics_per_page'];
			
			foreach ($current_files as $file) {
				$ext = strtolower(substr($file, strrpos($file, '.') +1));
				$name = substr($file, 0, strrpos($file, '.'));
				if ($cfg['types'][$ext] == 1) {
					// pictures
					$current_tmp_path = $cfg['tmp_path'] . $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path;
					if (!file_exists($current_tmp_path)) {
						createTmpDirs($cfg['tmp_path'], $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path);
					}
					$tmpfilename = 't' . $cfg['thumb_size'] . '_' . $name . '.jpg';
					if (!file_exists($current_tmp_path . $tmpfilename)) {
						$image = new Image_Toolbox($filesystem_current_path . $file);
						$image->newOutputSize($cfg['thumb_size'], 0, false, true);
						$image->setResizeMethod($cfg['thumbnail_resize_method']);
						$image->save($current_tmp_path . $tmpfilename, 'jpg', $cfg['jpeg_quality']);
						unset($image);
					}
					// generate arrays
					if ($u >= $per_page_start && $u < $per_page_end) {
						list($current_dir_files[$i]['resized_width'], $current_dir_files[$i]['resized_height']) = getimagesize($current_tmp_path . $tmpfilename);
						if ($current_dir_files[$i]['resized_height'] > $current_dir_files_highest) {
							$current_dir_files_highest = $current_dir_files[$i]['resized_height'];
						}
						if ($current_dir_files[$i]['resized_width'] > $current_dir_files_widest) {
							$current_dir_files_widest = $current_dir_files[$i]['resized_width'];
						}
						$current_dir_files[$i]['href'] = $web_pAG_path_rel . $web_current_path . $file;
						$current_dir_files[$i]['name'] = $file;
						$current_dir_files[$i]['img'] = $web_pAG_path_rel . '__phpAutoGallery__picLoaderTmp/' . $web_pAG_path_abs . $web_current_path . $tmpfilename;
						$current_dir_files[$i]['type'] = 1;
						$i++;
					}
					$current_dir_filecount[1]++;
					$u++;
				}
				elseif ($cfg['types'][$ext] == 2) {
					// video filetypes
					$current_dir_files[$i]['href'] = $web_pAG_path_rel . '__phpAutoGallery__videoLoader/' . $web_current_path . $file;
					$current_dir_files[$i]['name'] = $file;
					$current_dir_files[$i]['img'] = $web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/' . $cfg['icon_video_' . $ext];
					$current_dir_files[$i]['type'] = 2;
					$current_dir_filecount[2]++;
					$i++;
				}
				else {
					// other filetypes
					if ($file != '.htaccess') {
						$current_dir_files[$i]['href'] = $web_pAG_path_rel . '__phpAutoGallery__originalLoader/' . $web_current_path . $file;
						$current_dir_files[$i]['name'] = $file;
						$current_dir_files[$i]['img'] = $web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/other.gif';
						$current_dir_files[$i]['type'] = 0;
						$current_dir_filecount[0]++;
						$i++;
					}
				}
			}
			$current_dir_start_pic = $per_page_start + 1;
			if ($per_page_end < $current_dir_filecount[1]) {
				$current_dir_end_pic = $per_page_end;
			}
			else {
				$current_dir_end_pic = $current_dir_filecount[1];
			}
			// generate per page view links
			$per_page_array = '';
			$per_page_prev = '';
			$per_page_next = '';
			$y = 0;
			if ($per_page_start != 0) {
				$per_page_prev = '?offset=' . ($per_page_start - $cfg['pics_per_page']);
			}
			for ($x = 0; ($x < $current_dir_filecount[1]) && ($current_dir_filecount[1] > $cfg['pics_per_page']); $x += $cfg['pics_per_page']) {
				$per_page_array[$y]['name'] = ($x / $cfg['pics_per_page']) + 1;
				if ($per_page_start != $x) {
					$per_page_array[$y]['href'] = '?offset=' . $x;
				}
				else {
					$per_page_array[$y]['href'] = '';
				}
				$y++;
			}
			if (($current_dir_filecount[1] - $per_page_end) > 0) {
				$per_page_next = '?offset=' . $per_page_end;
			}
		}
		else {
			$current_dir_files = 0;
		}
		
		// assign smarty template variables
		$template->assign('vCurrentDirName', $current_dir_name);
		$template->assign('arrCurrentNav', $current_nav);
		$template->assign('arrCurrentDirDirs', $current_dir_dirs);
		$template->assign('arrCurrentDirFiles', $current_dir_files);
		$template->assign('arrCurrentDirFilecount', $current_dir_filecount);
		$template->assign('vCurrentDirDircount', $current_dir_dircount);
		$template->assign('vCurrentDirBytecount', $current_dir_bytecount);
		$template->assign('vCurrentDirBytecountTotal', $current_dir_bytecounttotal);
		$template->assign('arrCurrentDirFilesHighestHeight', $current_dir_files_highest);
		$template->assign('arrCurrentDirFilesWidestWidth', $current_dir_files_widest);
		$template->assign('vCurrentDirStartPic', $current_dir_start_pic);
		$template->assign('vCurrentDirEndPic', $current_dir_end_pic);
		$template->assign('arrViewPages', $per_page_array);
		$template->assign('vViewPrev', $per_page_prev);
		$template->assign('vViewNext', $per_page_next);
		// assign smarty template
		$template->assign('internContentTemplate', 'dirlisting.tpl');
		
	} // end of dir mode
	else {
		// picture view mode
		
		// where and who am i
		$filesystem_current_path = $filesystem_pAG_path_abs . substr($url_request_part, 0, strrpos($url_request_part, '/')) . '/';
		$web_current_path = substr($url_request_part, 0, strrpos($url_request_part, '/')) . '/';
		$file = substr($url_request_part, strrpos($url_request_part, '/') + 1);
		if ($web_current_path == '/') {
			$file = $url_request_part;
			$web_current_path = '';
		}
		else {
			$file = substr($url_request_part, strrpos($url_request_part, '/') + 1);
		}
		
		$ext = strtolower(substr($file, strrpos($file, '.') +1));
		$name = substr($file, 0, strrpos($file, '.'));
		
		// get navigation path array
		$nav_dummy = explode('/', substr($web_current_path, 0, -1));
		if ($url_request_part == '') {
			$current_nav[0]['href'] = '';
		}
		else {
			$current_nav[0]['href'] = $web_pAG_path_rel;
		}
		$current_nav[0]['name'] = $cfg['root_folder_name'];
		for ($i = 0; isset($nav_dummy[$i]) && $nav_dummy[$i] !== ''; $i++) {
			$current_nav[($i + 1)]['name'] = $nav_dummy[$i];
			$current_nav[($i + 1)]['href'] = $web_pAG_path_rel;
			for ($u = 0; $u <= $i; $u++) {
				$current_nav[($i + 1)]['href'] .= $nav_dummy[$u] . '/';
			}
		}
		
		// get current directory's files and subdirectories
		$current_files = getDirFiles($filesystem_current_path);
		$current_dirs = getDirDirs($filesystem_current_path);
		$current_picture_files = getDirPictureFiles($filesystem_current_path);
		
		// check supported extensions
		if ($cfg['types'][$ext] == 1) {
			// picture handling mode
			// picture count and current file position
			$current_picture_files_count = sizeof($current_picture_files);
			$current_picture_file_position = getArrayPosition($current_picture_files, $file);
		
			// get picture infos (original)
			list($current_picture['info']['width'], $current_picture['info']['height']) = getimagesize($filesystem_pAG_path_abs . $url_request_part);
			$current_picture['info']['filesize'] = filesize_human($filesystem_pAG_path_abs . $url_request_part);
						
			// determine display width
			if (isset($_GET['size'])) {
				if (isset($cfg['view_sizes'][$_GET['size']])) {
					$width = $cfg['view_sizes'][$_GET['size']];
				}
				else {
					$width = $cfg['view_sizes'][$cfg['default_view']];
					unset($_GET['size']);
				}
			}
			elseif ($_GET['orig'] == 1 && $cfg['allow_original_view']) {
				$width = 0;
			}
			else {
				$width = $cfg['view_sizes'][$cfg['default_view']];
				unset($_GET['orig']);
			}
			
			// generate current (resized) picture
			if ($width !== 0) {
				$current_tmp_path = $cfg['tmp_path'] . $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path;
				if (!file_exists($current_tmp_path)) {
					createTmpDirs($cfg['tmp_path'], $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path);
				}
				
				$tmpfilename = $width . '_' . $name . '.jpg';
				if (!file_exists($current_tmp_path . $tmpfilename)) {
					$image = new Image_Toolbox($filesystem_current_path . $file);
					$image->newOutputSize($width, 0, false, false);
					if ($cfg['logo_image'] != '') {
						$image->addImage($filesystem_pAG_path_abs . '__phpAutoGallery/img/' . $cfg['logo_image']);
						$image->blend($cfg['logo_position_x'], $cfg['logo_position_y']);
					}
					$image->save($current_tmp_path . $tmpfilename, 'jpg', $cfg['jpeg_quality']);
					unset($image);
				}
				
				// get picture infos (resized)
				list($current_picture['resized_width'], $current_picture['resized_height']) = getimagesize($current_tmp_path . $tmpfilename);
				
				$current_picture['img'] = $web_pAG_path_rel . '__phpAutoGallery__picLoaderTmp/' . $web_pAG_path_abs . $web_current_path . $tmpfilename;
				$current_picture['name'] = $file;
			}
			else {
				$current_picture['name'] = $file;
				$current_picture['img'] = $web_pAG_path_rel . '__phpAutoGallery__picLoader/' . $web_current_path . $file;
				$current_picture['resized_width'] = $current_picture['info']['width'];
				$current_picture['resized_height'] = $current_picture['info']['height'];
			}
			
			//generate view size links
			$view_size_links = array();
			$i = 0;
			foreach ($cfg['view_sizes'] as $view_size => $view_width) {
				if (((isset($_GET['size']) && $_GET['size'] == $view_size) || (!isset($_GET['size']) && $view_size == $cfg['default_view'])) && $_GET['orig'] != 1) {
					$view_size_links[$i]['href'] = '';
					$view_size_links[$i]['name'] = $cfg['view_sizes'][$view_size];
				}
				else {
					$view_size_links[$i]['href'] = $web_pAG_path_rel . $web_current_path . $file . '?size=' . $view_size;
					$view_size_links[$i]['name'] = $cfg['view_sizes'][$view_size];
				}
				$i++;
			}
			// generate original link if allowed
			$view_original_link = array();
			if ($cfg['allow_original_view']) {
				if ($_GET['orig'] == 1) {
					$view_original_link['href'] = '';
					$view_original_link['name'] = 'original';
					$view_original_link['allowed'] = true;
				}
				else {
					$view_original_link['href'] = $web_pAG_path_rel . $web_current_path . $file . '?orig=1';
					$view_original_link['name'] = 'original';
					$view_original_link['allowed'] = true;
				}
			}
			else {
				$view_original_link['href'] = '';
				$view_original_link['name'] = 'original';
				$view_original_link['allowed'] = false;
			}
			
			// get next/previous pictures filenames
			//prev
			if ($current_picture_file_position == 0) {
				$prev_picture['href'] = '';
				$prev_picture['img'] = '';
				$prev_picture['name'] = '';
				$prev_picture_file = '';
			}
			else {
				$prev_picture_file = $current_picture_files[$current_picture_file_position - 1];
				$prev_name = substr($prev_picture_file, 0, strrpos($prev_picture_file, '.'));
				$current_tmp_path = $cfg['tmp_path'] . $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path;
				if (!file_exists($current_tmp_path)) {
						createTmpDirs($cfg['tmp_path'], $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path);
				}
				$tmpfilename = 't' . $cfg['next_previous_size'] . '_' . $prev_name . '.jpg';
				if (!file_exists($current_tmp_path . $tmpfilename)) {
					$image = new Image_Toolbox($filesystem_current_path . $file);
					$image->newOutputSize($cfg['next_previous_size'], 0, false, true);
					$image->save($current_tmp_path . $tmpfilename, 'jpg', $cfg['jpeg_quality']);
					unset($image);
				}
				list($prev_picture['resized_width'], $prev_picture['resized_height']) = getimagesize($current_tmp_path . $tmpfilename);
				$prev_picture['href'] = $web_pAG_path_rel . $web_current_path . $prev_picture_file;
				$prev_picture['img'] = $web_pAG_path_rel . '__phpAutoGallery__picLoaderTmp/' . $web_pAG_path_abs . $web_current_path . $tmpfilename;
				$prev_picture['name'] = $prev_picture_file;
			}
			//next
			if ($current_picture_files_count == ($current_picture_file_position + 1)) {
				$next_picture['href'] = '';
				$next_picture['img'] = '';
				$next_picture['name'] = '';
				$next_picture_file = '';
			}
			else {
				$next_picture_file = $current_picture_files[$current_picture_file_position + 1];
				$next_name = substr($next_picture_file, 0, strrpos($next_picture_file, '.'));
				$current_tmp_path = $cfg['tmp_path'] . $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path;
				if (!file_exists($current_tmp_path)) {
						createTmpDirs($cfg['tmp_path'], $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path);
				}
				$tmpfilename = 't' . $cfg['next_previous_size'] . '_' . $next_name . '.jpg';
				if (!file_exists($current_tmp_path . $tmpfilename)) {
					$image = new Image_Toolbox($filesystem_current_path . $file);
					$image->newOutputSize($cfg['next_previous_size'], 0, false, true);
					$image->save($current_tmp_path . $tmpfilename, 'jpg', $cfg['jpeg_quality']);
					unset($image);
				}
				list($next_picture['resized_width'], $next_picture['resized_height']) = getimagesize($current_tmp_path . $tmpfilename);
				$next_picture['href'] = $web_pAG_path_rel . $web_current_path . $next_picture_file;
				$next_picture['img'] = $web_pAG_path_rel . '__phpAutoGallery__picLoaderTmp/' . $web_pAG_path_abs . $web_current_path . $tmpfilename;
				$next_picture['name'] = $next_picture_file;
			}
			
		}
		
		// assign smarty variables
		$template->assign('arrCurrentNav', $current_nav);
		$template->assign('vCurrentPictureFilecount', $current_picture_files_count);
		$template->assign('vCurrentPictureFilenumber', ($current_picture_file_position + 1));
		$template->assign('arrCurrentPicture', $current_picture);
		$template->assign('arrViewSizeLinks', $view_size_links);
		$template->assign('arrViewOriginalLink', $view_original_link);
		$template->assign('arrPrevPicture', $prev_picture);
		$template->assign('arrNextPicture', $next_picture);
		// assign smarty template
		$template->assign('internContentTemplate', 'viewpic.tpl');
	
	} // end of pic view mode
	
}

// calculate processing time
$pt_end = getmicrotime();
$pt = sprintf("%0.4f", $pt_end - $pt_start);
$template->assign('vProcessingTime', $pt);

// display smarty template
$template->debugging = false;
$template->display('index.tpl');

?>