<?php
/**
 * wrapper.php -- main script for phpAutoGallery
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
 
require_once ('./include/functions.inc.php');
require_once ('./include/Image_Toolbox.class.php');
require_once ('./include/internal_config.inc.php');
require_once ('./config/config.inc.php');
require_once ('./smarty/Smarty.class.php');

// Special Loaders
if (strstr($_SERVER["REQUEST_URI"],'__phpAutoGallery__picLoader/')) {
	include ('loader/picloader.php');
}
else if (strstr($_SERVER["REQUEST_URI"],'__phpAutoGallery__picLoaderTmp/')) {
	include ('loader/picloadertmp.php');
}
else if (strstr($_SERVER["REQUEST_URI"],'__phpAutoGallery__cssLoader/')) {
	include ('loader/cssloader.php');
}
else if (strstr($_SERVER["REQUEST_URI"],'__phpAutoGallery__jsLoader/')) {
	include ('loader/jsloader.php');
}
else if (strstr($_SERVER["REQUEST_URI"],'__phpAutoGallery__videoLoader/')) {
	include ('loader/videoloader.php');
}
else if (strstr($_SERVER["REQUEST_URI"],'__phpAutoGallery__phpLoader/')) {
	session_start();
	list($d1, $d2) = explode("?", $_SERVER["REQUEST_URI"]);
	$phpfilename = substr($d1, (strrpos($d1, "/") + 1));
	include ('php/' . $phpfilename);
}
else {
	/* standard wrapper */
	
	// standard HTTP header
	header('Content-Type: text/html; charset=utf-8');
	
	// session stuff
	session_start();
	if (isset($_SESSION['pAG']['__admin'])) {
		//echo "admin mode<br>";
	}
	
	$pt_start = getmicrotime();
	
	// no script timeout. (thumb generation may take some time)
	set_time_limit(0);
	
	$template = new Smarty;
	$template->template_dir = './templates/'; 
	$template->compile_dir = './smarty/templates_c/';
	
	$filesystem_root_path = str_replace($HTTP_SERVER_VARS['SCRIPT_NAME'], "/", $HTTP_SERVER_VARS['SCRIPT_FILENAME']);
	$filesystem_pAG_path_abs = str_replace($cfg['wrapper_path'], '', str_replace("\\", "/", realpath($HTTP_SERVER_VARS['SCRIPT_FILENAME'])));
	$filesystem_pAG_path_rel = '/' . str_replace($filesystem_root_path, '', $filesystem_pAG_path_abs);
	$web_pAG_path_abs = $HTTP_SERVER_VARS['SERVER_NAME'] . $filesystem_pAG_path_rel;
	$web_pAG_path_rel = $filesystem_pAG_path_rel;
	
	//echo "root_path:".$filesystem_root_path."<br>";
	//echo 'redirect_url: '.$HTTP_SERVER_VARS['REDIRECT_URL'].'<br>';
	
	if ($HTTP_SERVER_VARS['REDIRECT_URL'] . '/' === $web_pAG_path_rel) {
		// special root dir without trailing slash
		$url_request_part = '';
	}
	elseif ($web_pAG_path_rel == '/') {
		$url_request_part = utf8_decode(substr($HTTP_SERVER_VARS['REDIRECT_URL'], 1));
	}
	else {
		$url_request_part = utf8_decode(str_replace($web_pAG_path_rel, '', $HTTP_SERVER_VARS['REDIRECT_URL']));
	}
	
	$template->assign('vCurrentRequest', '/' . $url_request_part);
	$template->assign('vRootPath', $web_pAG_path_rel);
	$template->assign('vVersion', $cfg['version']);
	$template->assign('vCopyright', $cfg['copyright']);
	$template->assign('vGalleryName', $cfg['gallery_name']);
	
	// quicknav redirect
	if (isset($_POST['submit_quicknav'])) {
		header('Location: '.$_POST['quicknav']);
	}
	
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
			//echo 'file: '.$filesystem_current_path.'<br>';
			$web_current_path = $url_request_part;
	
			// get navigation path array
			$nav_dummy = explode('/', substr($web_current_path, 0, -1));
			if ($url_request_part == '') {
				$current_nav[0]['href'] = '';
			}
			else {
				$current_nav[0]['href'] = utf8_encode($web_pAG_path_rel);
			}
			$current_nav[0]['name'] = utf8_encode($cfg['root_folder_name']);
			$current_nav_current['href'] = '';
			$current_dummy_name = array_pop($nav_dummy);
			for ($i = 0; isset($nav_dummy[$i]); $i++) {
				$current_nav[($i + 1)]['name'] = utf8_encode(samba2workaround($nav_dummy[$i]));
				$current_nav[($i + 1)]['href'] = utf8_encode($web_pAG_path_rel);
				for ($u = 0; $u <= $i; $u++) {
					$current_nav[($i + 1)]['href'] .= utf8_encode($nav_dummy[$u] . '/');
				}
			}
			if ($url_request_part != '') {
				$current_nav[($i + 1)]['href'] = '';
				$current_nav[($i + 1)]['name'] = utf8_encode(samba2workaround($current_dummy_name));
			}
			if (!$current_dummy_name) {
				$current_dir_name = utf8_encode($cfg['root_folder_name']);
			}
			else {
				$current_dir_name = utf8_encode(samba2workaround($current_dummy_name));
			}
			
			// get current directory's files and subdirectories
			$current_files = getDirFiles($filesystem_current_path);
			$current_dirs = getDirDirs($filesystem_current_path);
			$whole_tree = array();
			getDirTree($filesystem_pAG_path_abs, $whole_tree);
	
			$dummy_ret1 = getDirSize($filesystem_current_path);
			$dummy_ret2 = getDirSizeTotal($filesystem_current_path);
			
			$current_dir_info['name'] = $current_dir_name;
			$current_dir_info['totalsize'] = humansize($dummy_ret2[2]);
			$current_dir_info['totalfiles'] = $dummy_ret2[0];
			$current_dir_info['totaldirs'] = $dummy_ret2[1];
			$current_dir_info['size'] = humansize($dummy_ret1[2]);
			$current_dir_info['files'] = $dummy_ret1[0];
			$current_dir_info['dirs'] = $dummy_ret1[1];
			$current_dir_info['totalpictures'] = $dummy_ret2[3];
			$current_dir_info['totalvideos'] = $dummy_ret2[4];
			$current_dir_info['totalothers'] = $dummy_ret2[5];
			$current_dir_info['pictures'] = $dummy_ret1[3];
			$current_dir_info['videos'] = $dummy_ret1[4];
			$current_dir_info['others'] = $dummy_ret1[5];
			$current_dir_info['date'] = strftime($cfg['timeformat'], filemtime($filesystem_current_path));
			
			// description
			$descfilename = $filesystem_current_path . $cfg['folder_description_name'] . '.' . $cfg['description_extension'];
			if (file_exists($descfilename)) {
				$current_dir_info['description'] = loadTextFile($descfilename);
			}
			
			$current_dir_dirs = array();
			if (isset($current_dirs[0])) {
				$i = 0;
				foreach ($current_dirs as $dir) {
					$current_dir_dirs[$i]['href'] = utf8_encode($web_pAG_path_rel . $url_request_part . $dir['name']);
					$current_dir_dirs[$i]['name'] = utf8_encode(samba2workaround($dir['name']));
					$current_dir_dirs[$i]['img'] = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/' . $cfg['icon_folder']);
					$current_dir_dirs[$i]['totalsize'] = humansize($dir['totalsize']);
					$current_dir_dirs[$i]['totalfiles'] = $dir['totalfiles'];
					$current_dir_dirs[$i]['totaldirs'] = $dir['totaldirs'];
					$current_dir_dirs[$i]['size'] = humansize($dir['size']);
					$current_dir_dirs[$i]['files'] = $dir['files'];
					$current_dir_dirs[$i]['dirs'] = $dir['dirs'];
					$current_dir_dirs[$i]['totalpictures'] = $dir['totalpictures'];
					$current_dir_dirs[$i]['totalvideos'] = $dir['totalvideos'];
					$current_dir_dirs[$i]['totalothers'] = $dir['totalothers'];
					$current_dir_dirs[$i]['pictures'] = $dir['pictures'];
					$current_dir_dirs[$i]['videos'] = $dir['videos'];
					$current_dir_dirs[$i]['others'] = $dir['others'];
					$current_dir_dirs[$i]['date'] = strftime($cfg['timeformat'], $dir['timestamp']);
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
					$ext = strtolower(substr($file['name'], strrpos($file['name'], '.') +1));
					$name = substr($file['name'], 0, strrpos($file['name'], '.'));
					if (getFileType($file['name']) == 1) {
						// pictures
						$current_tmp_path = $cfg['tmp_path'] . $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path;
						if (!file_exists($current_tmp_path)) {
							createTmpDirs($cfg['tmp_path'], $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path);
						}
						$tmpfilename = 't' . $cfg['thumb_size'] . '_' . $name . '.jpg';
						if (!file_exists($current_tmp_path . $tmpfilename)) {
							$image = new Image_Toolbox($filesystem_current_path . $file['name']);
							$image->newOutputSize($cfg['thumb_size'], 0, false, true);
							$image->setResizeMethod($cfg['thumbnail_resize_method']);
							$image->save($current_tmp_path . $tmpfilename, 'jpg', $cfg['jpeg_quality']);
							@chmod($current_tmp_path . $tmpfilename, 0777);
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
							$current_dir_files[$i]['href'] = utf8_encode($web_pAG_path_rel . $web_current_path . $file['name']);
							$current_dir_files[$i]['name'] = utf8_encode(samba2workaround($file['name']));
							$current_dir_files[$i]['img'] = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoaderTmp/' . $web_pAG_path_abs . $web_current_path . $tmpfilename);
							$current_dir_files[$i]['size'] = humansize($file['size']);
							$current_dir_files[$i]['date'] = strftime($cfg['timeformat'], $file['timestamp']);
							$current_dir_files[$i]['type'] = 1;
							$i++;
						}
						$current_dir_filecount[1]++;
						$u++;
					}
					elseif (getFileType($file['name']) == 2) {
						// video filetypes
						$current_dir_files[$i]['href'] = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__videoLoader/' . $web_current_path . $file['name']);
						$current_dir_files[$i]['name'] = utf8_encode(samba2workaround($file['name']));
						$current_dir_files[$i]['img'] = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/' . $cfg['icon_video_' . $ext]);
						$current_dir_files[$i]['size'] = humansize($file['size']);
						$current_dir_files[$i]['date'] = strftime($cfg['timeformat'], $file['timestamp']);
						$current_dir_files[$i]['type'] = 2;
						$current_dir_filecount[2]++;
						$i++;
					}
					else {
						// other valid filetypes (.txt files, etc...)
						$current_dir_files[$i]['href'] = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__originalLoader/' . $web_current_path . $file['name']);
						$current_dir_files[$i]['name'] = utf8_encode(samba2workaround($file['name']));
						$current_dir_files[$i]['img'] = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/other.gif');
						$current_dir_files[$i]['size'] = $file['size'];
						$current_dir_files[$i]['date'] = $file['date'];
						$current_dir_files[$i]['type'] = 0;
						$current_dir_filecount[0]++;
						$i++;
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
			$template->assign('arrCurrentDirInfo', $current_dir_info);
			$template->assign('arrWholeTree', $whole_tree);
			$template->assign('arrCurrentNav', $current_nav);
			$template->assign('arrCurrentDirDirs', $current_dir_dirs);
			$template->assign('arrCurrentDirFiles', $current_dir_files);
			$template->assign('arrCurrentDirFilecount', $current_dir_filecount);
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
				$current_nav[0]['href'] = utf8_encode($web_pAG_path_rel);
			}
			$current_nav[0]['name'] = utf8_encode($cfg['root_folder_name']);
			for ($i = 0; isset($nav_dummy[$i]) && $nav_dummy[$i] !== ''; $i++) {
				$current_nav[($i + 1)]['name'] = utf8_encode(samba2workaround($nav_dummy[$i]));
				$current_nav[($i + 1)]['href'] = utf8_encode($web_pAG_path_rel);
				for ($u = 0; $u <= $i; $u++) {
					$current_nav[($i + 1)]['href'] .= utf8_encode($nav_dummy[$u] . '/');
				}
			}
			
			// get current directory's files and subdirectories
			$current_files = getDirFiles($filesystem_current_path);
			$current_dirs = getDirDirs($filesystem_current_path);
			$whole_tree = array();
			getDirTree($filesystem_pAG_path_abs, $whole_tree);
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
							if (file_exists($filesystem_pAG_path_abs . '__phpAutoGallery/img/' . $cfg['logo_image'])) {
								$image->addImage($filesystem_pAG_path_abs . '__phpAutoGallery/img/' . $cfg['logo_image']);
								$image->blend($cfg['logo_position_x'], $cfg['logo_position_y']);
							}
						}
						$image->save($current_tmp_path . $tmpfilename, 'jpg', $cfg['jpeg_quality']);
						@chmod($current_tmp_path . $tmpfilename, 0777);
						unset($image);
					}
					
					// get picture infos (resized)
					list($current_picture['resized_width'], $current_picture['resized_height']) = getimagesize($current_tmp_path . $tmpfilename);
					
					$current_picture['img'] = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoaderTmp/' . $web_pAG_path_abs . $web_current_path . $tmpfilename);
					$current_picture['name'] = utf8_encode(samba2workaround($file));
					// description
   					$descfilename = substr($filesystem_root_path, 0, -1) . $web_pAG_path_rel . $web_current_path . $name . '.' . $cfg['description_extension'];
					if (file_exists($descfilename)) {
						$current_picture['description'] = loadTextFile($descfilename);
					}
				}
				else {
					$current_picture['name'] = utf8_encode(samba2workaround($file));
					$current_picture['img'] = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/' . $web_current_path . $file);
					$current_picture['resized_width'] = $current_picture['info']['width'];
					$current_picture['resized_height'] = $current_picture['info']['height'];
				}
				
				//generate view size links
				$view_size_links = array();
				$i = 0;
				foreach ($cfg['view_sizes'] as $view_size => $view_width) {
					if (((isset($_GET['size']) && $_GET['size'] == $view_size) || (!isset($_GET['size']) && $view_size == $cfg['default_view'])) && $_GET['orig'] != 1) {
						$view_size_links[$i]['href'] = '';
						$view_size_links[$i]['name'] = utf8_encode($cfg['view_sizes'][$view_size]);
					}
					else {
						$view_size_links[$i]['href'] = utf8_encode($web_pAG_path_rel . $web_current_path . $file . '?size=' . $view_size);
						$view_size_links[$i]['name'] = utf8_encode($cfg['view_sizes'][$view_size]);
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
						$view_original_link['href'] = utf8_encode($web_pAG_path_rel . $web_current_path . $file . '?orig=1');
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
					$prev_picture_file = $current_picture_files[$current_picture_file_position - 1]['name'];
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
					$prev_picture['href'] = utf8_encode($web_pAG_path_rel . $web_current_path . $prev_picture_file);
					$prev_picture['img'] = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoaderTmp/' . $web_pAG_path_abs . $web_current_path . $tmpfilename);
					$prev_picture['name'] = utf8_encode(samba2workaround($prev_picture_file));
				}
				//next
				if ($current_picture_files_count == ($current_picture_file_position + 1)) {
					$next_picture['href'] = '';
					$next_picture['img'] = '';
					$next_picture['name'] = '';
					$next_picture_file = '';
				}
				else {
					$next_picture_file = $current_picture_files[$current_picture_file_position + 1]['name'];
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
					$next_picture['href'] = utf8_encode($web_pAG_path_rel . $web_current_path . $next_picture_file);
					$next_picture['img'] = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoaderTmp/' . $web_pAG_path_abs . $web_current_path . $tmpfilename);
					$next_picture['name'] = utf8_encode(samba2workaround($next_picture_file));
				}
				
			}
			
			// assign smarty variables
			$template->assign('arrCurrentNav', $current_nav);
			$template->assign('arrWholeTree', $whole_tree);
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

}
?>