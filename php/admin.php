<?php
/**
 * admin.php -- admin main script
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
 
// no script timeout. (thumb generation may take some time)
set_time_limit(0);

// where am i stuff
$thisurl = $_SERVER['REDIRECT_URL'];

$filesystem_root_path = str_replace($HTTP_SERVER_VARS['SCRIPT_NAME'], "/", $HTTP_SERVER_VARS['SCRIPT_FILENAME']);

$filesystem_pAG_path_abs = str_replace($cfg['wrapper_path'], '', str_replace("\\", "/", realpath($HTTP_SERVER_VARS['SCRIPT_FILENAME'])));
$filesystem_pAG_path_rel = '/' . str_replace($filesystem_root_path, '', $filesystem_pAG_path_abs);
$web_pAG_path_abs = $HTTP_SERVER_VARS['SERVER_NAME'] . $filesystem_pAG_path_rel;
$web_pAG_path_rel = $filesystem_pAG_path_rel;

if (isset($_REQUEST['path'])) {
	list($working_path, $d2) = explode("?", str_replace('http://' . $web_pAG_path_abs, '', $_REQUEST['path']));
	$working_path = '/' . urldecode($working_path);
}
else {
	if (isset($_POST['submit_quicknav'])) {
		list($_REQUEST['newpath']) = explode("&", str_replace($thisurl."?newpath=", "", $_POST['quicknav']));
	}
	if ($web_pAG_path_rel != "/") {
		$working_path = "/" . str_replace($web_pAG_path_rel, "", $_REQUEST['newpath']);
	}
	else {
		$working_path = $_REQUEST['newpath'];
	}
}

if ($working_path . '/' === $web_pAG_path_rel) {
	// special root dir without trailing slash
	$url_request_part = '';
}
elseif ($web_pAG_path_rel == '/') {
	$url_request_part = utf8_decode(substr($working_path, 1));
}
else {
	$url_request_part = utf8_decode(str_replace($web_pAG_path_rel, '', substr($working_path, 1)));
}

if (!is_file($filesystem_pAG_path_abs . $url_request_part) && !$_REQUEST['file']) {
	$mode = "dir";
	if ((substr($url_request_part, -1, 1) != '/') && ($url_request_part != '')) {
		$url_request_part .= '/';
		$working_path .= '/';
	}
	$web_current_path = $url_request_part;
	$path_for_link = substr($web_pAG_path_rel, 0, -1) . $working_path;
}
else {
	$mode = "file";
	$web_current_path = substr($url_request_part, 0, strrpos($url_request_part, '/')) . '/';	
	if ($web_current_path == '/') {
		$thisfile = $url_request_part;
		$web_current_path = '';
	}
	else {
		$thisfile = substr($url_request_part, strrpos($url_request_part, '/') + 1);
	}
	if (!$_REQUEST['file']) {
		$_REQUEST['file'] = $thisfile;
	}
	$working_path = substr($working_path, 0, strrpos($working_path, '/') + 1);
	$path_for_link = substr($web_pAG_path_rel, 0, -1) . $working_path;
}

$filesystem_current_path = $filesystem_pAG_path_abs . utf8_decode(substr($working_path, 1));

/////////////////////

echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';
echo '<title>'.$cfg['gallery_name'].'&nbsp;::&nbsp;admin</title>';
echo '<link rel="stylesheet" type="text/css" href="'.$web_pAG_path_rel.'__phpAutoGallery__cssLoader/css/style.css"/>';
echo '<script src="'.$web_pAG_path_rel.'__phpAutoGallery__jsLoader/javascript/functions.js" type="text/javascript"></script>';
echo '</head>';
echo '<body class="admin">';

if (isset($_POST['submit_admin_login'])) {
	if ($_POST['username'] == $cfg['admin']['username'] && $_POST['password'] == $cfg['admin']['password']) {
		$_SESSION['pAG']['__admin'] = true;
	}
	else {
		$error = "wrong username or password!";
	}
}

if (!isset($_SESSION['pAG']['__admin'])) {
	// not logged in as admin
	echo '<br><br><br><br><br><br>';
	if (isset($error)) {
		echo '<div class="error">';
		echo $error;
		echo '</div>';
	}
	echo '<div align="center">';
	echo '<form method="post" action="'.$thisurl.'">';
	echo '<table cellspacing="0">';
	echo '<tr>';
	echo '<td colspan="2" class="adminlist_head_first"><b>Login</b></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td class="adminlist_first">username</td>';
	echo '<td class="adminlist"><input type="text" name="username" value=""/></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td class="adminlist_first">password</td>';
	echo '<td class="adminlist"><input type="password" name="password" value=""/></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<input type="hidden" name="submit_admin_login" value="1"/>';
	echo '<input type="hidden" name="path" value="'.$_REQUEST['path'].'"/>';
	echo '<td class="adminlist_first" colspan="2"><input type="submit" name="submit_admin_login_button" value="login"/></td>';
	echo '</tr>';
	echo '</table>';
	echo '</form>';
	echo '</div>';
}
else {
	/* admin here */
	
	// default action:
	if (!isset($_REQUEST['action'])) {
		$_REQUEST['action'] = 'overview';
	}
	
	// what folder
	echo '<table cellspacing="0" class="adminmenu" border="0" width="100%">';
	echo '<form method="post" action="'.$thisurl.'">';
	echo '<tr>';
	echo '<td colspan="10" width="100%" class="adminfield_tree">';
		
	$whole_tree = array();
	getDirTree($filesystem_pAG_path_abs, $whole_tree);
	echo 'Directory:&nbsp;';
	echo '<select name="quicknav" onChange="Go(this.form.quicknav.options[this.form.quicknav.options.selectedIndex].value)">';
	foreach ($whole_tree as $entry) {
		$sel = '';
		if ($entry['active'] == 1) {
			$sel = ' selected';
		}
		echo '<option'.$sel.' class="'.$entry['class'].'" value="'.$thisurl.'?newpath='.$entry['href'].'&action='.$_REQUEST['action'].'">'.$entry['prefix'].$entry['name'].'</option>';
	}
	echo '</select>';
	echo '<input type="hidden" name="action" value="'.$_REQUEST['action'].'">';
	echo '<input type="submit" name="submit_quicknav" value="go"/>';
	if ($_REQUEST['file']) {
		echo '&nbsp;&nbsp;File:&nbsp;<b>'.$_REQUEST['file'].'</b>';
		echo '&nbsp;(<a href="'.$thisurl.'?action=overview&newpath='.$path_for_link.'">back to folder</a>)';
	}
	echo '</td>';
	echo '</tr>';
	echo '</form>';
	
	/* menu */
	echo '<tr>';
	$class = 'adminfield1';
	if ($_REQUEST['action'] == 'overview') {
		$class = "adminfield1_active";
	}
	echo '<td class="'.$class.'">&nbsp;&nbsp;&nbsp;<a href="'.$thisurl.'?action=overview&newpath='.$path_for_link.'&file='.$_REQUEST['file'].'">overview</a>&nbsp;&nbsp;&nbsp;</td>';
	$class = 'adminfield1';
	if ($_REQUEST['action'] == 'description') {
		$class = "adminfield1_active";
	}
	echo '<td class="'.$class.'">&nbsp;&nbsp;&nbsp;<a href="'.$thisurl.'?action=description&newpath='.$path_for_link.'&file='.$_REQUEST['file'].'">description</a>&nbsp;&nbsp;&nbsp;</td>';
	$class = 'adminfield1';
	if ($_REQUEST['action'] == 'resizing') {
		$class = "adminfield1_active";
	}
	echo '<td class="'.$class.'">&nbsp;&nbsp;&nbsp;<a href="'.$thisurl.'?action=resizing&newpath='.$path_for_link.'&file='.$_REQUEST['file'].'">resizing</a>&nbsp;&nbsp;&nbsp;</td>';
	echo '<td width="100%" class="adminfield1_last">&nbsp;</td>';
	echo '</tr>';
	echo '</table>';	
	
	
	/* content */
	if ($_REQUEST['action'] == 'overview') {
		/* overview */
		if ($mode == "dir") {
			$current_files = getDirFiles($filesystem_current_path, 1);
			
			if (is_array($current_files)) {
				foreach ($current_files as $key => $file) {
					$ext = strtolower(substr($file['name'], strrpos($file['name'], '.') +1));
					$name = substr($file['name'], 0, strrpos($file['name'], '.'));
					if (getFileType($file['name']) == 1) {
						// pictures
						$current_tmp_path = $cfg['tmp_path'] . $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path;
						
						// thumb:
						$tmpfilename = 't' . $cfg['thumb_size'] . '_' . $name . '.jpg';
						if (!file_exists($current_tmp_path . $tmpfilename)) {
							// file doesnot exist
							$current_files[$key]['thumb_exists'] = 0;
						}
						else {
							// file exsists:
							$current_files[$key]['thumb_exists'] = 1;
						}
						
						// next/previous:
						$tmpfilename = 't' . $cfg['next_previous_size'] . '_' . $name . '.jpg';
						if (!file_exists($current_tmp_path . $tmpfilename)) {
							// file doesnot exist
							$current_files[$key]['next_previous_exists'] = 0;
						}
						else {
							// file exists
							$current_files[$key]['next_previous_exists'] = 1;
						}
		
						// resized pictures:
						foreach ($cfg['view_sizes'] as $viewsize) {
							$tmpfilename = $viewsize . '_' . $name . '.jpg';
							if (!file_exists($current_tmp_path . $tmpfilename)) {
								// file doesnot exist
								$current_files[$key]['resized_exists'][$viewsize] = 0;
							}
							else {
								// file exsists:
								$current_files[$key]['resized_exists'][$viewsize] = 1;
							}
						}
					} // end if picture
				} // end foreach
				
				// display the files overview results
				echo '<div style="padding:10px;">';
				echo '<table width="95%" cellspacing="0">';
				echo '<tr>';
				echo '<td colspan="15" class="adminlist_head_first"><b>image files</b></td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td class="adminlist_first"><i>filename</i></td>';
				echo '<td class="adminlist" align="center"><i>thumb</i></td>';
				if ($cfg['thumb_size'] != $cfg['next_previous_size']) {
					echo '<td class="adminlist" align="center"><i>next/previous</i></td>';
				}
				foreach ($cfg['view_sizes'] as $viewsize) {
					echo '<td class="adminlist" align="center"><i>'.$viewsize.'px</i></td>';
				}
				echo '</tr>';
				
				foreach ($current_files as $file) {
					echo '<tr>';
					echo '<td class="adminlist_first"><a title="edit this picture" href="'.$thisurl.'?action=overview&newpath='.$path_for_link.'&file='.utf8_encode($file['name']).'">'.utf8_encode($file['name']).'</a></td>';
					if ($file['thumb_exists'] == 1) {
						$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_green.png');
						$showiconalt = 'generated';
					}
					else {
						$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_red.png');
						$showiconalt = 'not generated';
					}
					echo '<td class="adminlist" align="center"><img src="'.$showicon.'"/ alt="'.$showiconalt.'"></td>';
					if ($cfg['thumb_size'] != $cfg['next_previous_size']) {
						if ($file['next_previous_exists'] == 1) {
							$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_green.png');
							$showiconalt = 'generated';
						}
						else {
							$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_red.png');
							$showiconalt = 'not generated';
						}
						echo '<td class="adminlist" align="center"><img src="'.$showicon.'"/ alt="'.$showiconalt.'"></td>';
					}
					foreach ($cfg['view_sizes'] as $viewsize) {
						if ($file['resized_exists'][$viewsize] == 1) {
							$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_green.png');
							$showiconalt = 'generated';
						}
						else {
							$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_red.png');
							$showiconalt = 'not generated';
						}
						echo '<td class="adminlist" align="center"><img src="'.$showicon.'"/ alt="'.$showiconalt.'"></td>';
					}	
					echo '</tr>';
				}
				
				echo '</table>';
				echo '</div>';
				
			} // end if array
			else {
				echo '<div style="padding:10px;">';
				echo '<table width="95%" cellspacing="0">';
				echo '<tr>';
				echo '<td colspan="15" class="adminlist_head_first"><b>image files</b></td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td colspan="15" class="adminlist_first"><i>no image files found</i></td>';
				echo '</tr>';
				echo '</div>';
			}
		}
		else {
			$file = array();
			$file['name'] = utf8_decode($_REQUEST['file']);
			$ext = strtolower(substr($file['name'], strrpos($file['name'], '.') +1));
			$name = substr($file['name'], 0, strrpos($file['name'], '.'));
			if (($file['filetype'] = getFileType($file['name'])) == 1) {
				// pictures
				$current_tmp_path = $cfg['tmp_path'] . $cfg['tmp_pAG_path'] . $web_pAG_path_abs . $web_current_path;
				
				// thumb:
				$tmpfilename = 't' . $cfg['thumb_size'] . '_' . $name . '.jpg';
				if (!file_exists($current_tmp_path . $tmpfilename)) {
					// file doesnot exist
					$file['thumb_exists'] = 0;
				}
				else {
					// file exsists:
					$file['thumb_exists'] = 1;
				}

				// next/previous:
				$tmpfilename = 't' . $cfg['next_previous_size'] . '_' . $name . '.jpg';
				if (!file_exists($current_tmp_path . $tmpfilename)) {
					// file doesnot exist
					$file['next_previous_exists'] = 0;
				}
				else {
					// file exists
					$file['next_previous_exists'] = 1;
				}

				// resized pictures:
				foreach ($cfg['view_sizes'] as $viewsize) {
					$tmpfilename = $viewsize . '_' . $name . '.jpg';
					if (!file_exists($current_tmp_path . $tmpfilename)) {
						// file doesnot exist
						$file['resized_exists'][$viewsize] = 0;
					}
					else {
						// file exsists:
						$file['resized_exists'][$viewsize] = 1;
					}
				}
			} // end if picture

			// display the files overview results
			echo '<div style="padding:10px;">';
			echo '<table width="95%" cellspacing="0">';
			echo '<tr>';
			echo '<td colspan="15" class="adminlist_head_first"><b>file</b></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td class="adminlist_first"><i>filename</i></td>';
			//echo '<td class="adminlist" align="center"><i>filetype</i></td>';
			echo '<td class="adminlist" align="center"><i>thumb</i></td>';
			if ($cfg['thumb_size'] != $cfg['next_previous_size']) {
				echo '<td class="adminlist" align="center"><i>next/previous</i></td>';
			}
			foreach ($cfg['view_sizes'] as $viewsize) {
				echo '<td class="adminlist" align="center"><i>'.$viewsize.'px</i></td>';
			}
			echo '</tr>';
			
			echo '<tr>';
			echo '<td class="adminlist_first"><b>'.utf8_encode($file['name']).'</b></td>';
			//echo '<td class="adminlist" align="center">'.$file['filetype'].'</td>';
			if ($file['thumb_exists'] == 1) {
				$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_green.png');
				$showiconalt = 'generated';
			}
			else {
				$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_red.png');
				$showiconalt = 'not generated';
			}
			echo '<td class="adminlist" align="center"><img src="'.$showicon.'"/ alt="'.$showiconalt.'"></td>';
			if ($cfg['thumb_size'] != $cfg['next_previous_size']) {
				if ($file['next_previous_exists'] == 1) {
					$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_green.png');
					$showiconalt = 'generated';
				}
				else {
					$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_red.png');
					$showiconalt = 'not generated';
				}
				echo '<td class="adminlist" align="center"><img src="'.$showicon.'"/ alt="'.$showiconalt.'"></td>';
			}
			foreach ($cfg['view_sizes'] as $viewsize) {
				if ($file['resized_exists'][$viewsize] == 1) {
					$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_green.png');
					$showiconalt = 'generated';
				}
				else {
					$showicon = utf8_encode($web_pAG_path_rel . '__phpAutoGallery__picLoader/__phpAutoGallery/img/status_red.png');
					$showiconalt = 'not generated';
				}
				echo '<td class="adminlist" align="center"><img src="'.$showicon.'"/ alt="'.$showiconalt.'"></td>';
			}	
			echo '</tr>';
			echo '</table>';
			echo '<br>';
			echo '&nbsp;&nbsp;<a href="'.$thisurl.'?action=overview&newpath='.$path_for_link.'">back to folder</a>';
			echo '</div>';
			echo '</form>';
		}
			
	} // end overview
	elseif ($_REQUEST['action'] == 'description') {
		/* description */
		if (isset($_POST['submit_admin_description_change'])) {
			// change
			if (trim($_POST['desc_text']) != "") {
				$fp = fopen(utf8_decode($_POST['desc_filename']), "w");
				if ($fp) {
					fwrite($fp, $_POST['desc_text']);
					fclose($fp);
				}
				else {
					echo "error opening desc file for writing!";
				}
			}
			else {
				// delete file, because there's no text.
				@unlink(utf8_decode($_POST['desc_filename']));
			}
		}
		
		// edit description
		if ($_REQUEST['file'] == "") {
			$desc_filename = $filesystem_current_path . "/" . $cfg['folder_description_name'] . "." . $cfg['description_extension'];
		}
		else {
			$filename_prefix = substr($_REQUEST['file'], 0, strrpos($_REQUEST['file'], '.'));
			$desc_filename = $filesystem_current_path . $filename_prefix . "." . $cfg['description_extension'];
		}
		if (file_exists(utf8_decode($desc_filename))) {
			$desc_text = "";
			$desc_all_lines = file(utf8_decode($desc_filename));
			foreach ($desc_all_lines as $line) {
				$desc_text .= $line;
			}
		}
		echo '<div style="padding:10px;">';
		echo '<table width="95%" cellspacing="0">';
		echo '<form method="post" action="'.$thisurl.'">';
		echo '<tr>';
		if ($_REQUEST['file'] == "") {
			$title = 'folder description';
		}
		else {
			$title = 'file description';
		}
		echo '<td class="adminlist_head_first"><b>'.$title.'</b></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td class="adminlist_first">';
		echo '<textarea name="desc_text" rows="7" cols="80">'.$desc_text.'</textarea>';
		echo '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td class="adminlist_first"><input type="submit" name="submit_admin_description" value="change"></td>';
		echo '</tr>';
		echo '</table>';
		echo '</div>';
		echo '<input type="hidden" name="newpath" value="'.$path_for_link.'">';
		echo '<input type="hidden" name="file" value="'.$_REQUEST['file'].'">';
		echo '<input type="hidden" name="action" value="description">';
		echo '<input type="hidden" name="submit_admin_description_change" value="1">';
		echo '<input type="hidden" name="desc_filename" value="'.$desc_filename.'">';
		echo '</form>';
	} // end description
	elseif ($_REQUEST['action'] == 'resizing') {
		/* resizing tools */
		if (isset($_POST['submit_admin_resize'])) {
			$i = 0;
			if ($_POST['method'] == "1") {
				echo '&nbsp;&nbsp;&nbsp;';
				echo "<b>generating missing images:</b><br>";
			}
			else {
				echo '&nbsp;&nbsp;&nbsp;';
				echo "<b>re-generating all images:</b><br>";
			}

			// this folder
			$whatpath = $filesystem_current_path;
			if ($_REQUEST['file'] == "") {
				echo '<br><div class="greentext">&nbsp;&nbsp;&nbsp;processing folder: <b>'.utf8_encode(str_replace($filesystem_root_path, "", $whatpath)).'</b></div>';
				flush();
			}
			else {
				echo '<br>';
				flush();
			}
			include ('./php/admin_resize.inc.php');
			
			// sub folders
			if ($_POST['subfolders'] == '1') {
				$subfoldertree = array();
				$dummy = $web_pAG_path_rel;
				$web_pAG_path_rel = '/';
				getDirTree($filesystem_current_path, $subfoldertree);
				$web_pAG_path_rel = $dummy;
				
				foreach ($subfoldertree as $key => $subfolder) {
					if ($key != 0) {
						$whatpath = substr($filesystem_current_path, 0, -1) . utf8_decode($subfolder['href']);
						echo '<br><div class="greentext">&nbsp;&nbsp;&nbsp;processing folder: <b>'.utf8_encode(str_replace($filesystem_root_path, "", $whatpath)).'</b></div>';
						flush();
						include ('./php/admin_resize.inc.php');
					}
				}
			}
			echo '<br>';
			echo '<a class="dummy_link" name="line'.$i++.'">&nbsp;&nbsp;&nbsp;</a>';
			echo '<b>all done!</b>';
			echo '<script language="JavaScript">scrolldown('.($i - 1).');</script>';
			flush();
		}
		else {
			echo '<div style="padding:10px;">';
			echo '<table width="95%" cellspacing="0">';
			echo '<form method="post" action="'.$thisurl.'#theend">';
			echo '<tr>';
			echo '<td class="adminlist_head_first"><b>resize tools</b></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td class="adminlist_first">';
			echo '<input type="radio" name="method" value="1" checked>generate only missing';
			echo '<input type="radio" name="method" value="2">regenerate all<br/>';
			echo '<br>';
			echo '<select multiple name="what[]" size="6">';
			echo '<option value="-1" selected>thumbnails</option>';
			if ($cfg['thumb_size'] != $cfg['next_previous_size']) {
				echo '<option value="-2">next / previous images</option>';
			}
			foreach ($cfg['view_sizes'] as $index => $viewsize) {
				echo '<option value="'.$index.'">'.$viewsize.'px images</option>';
			}
			echo '</select>';
			echo '<br/>';
			if ($_REQUEST['file'] == "") {
				echo '<input type="checkbox" name="subfolders" value="1">for all subfolders too';
				echo '<br/>';
			}
			echo '<input type="hidden" name="submit_admin_resize" value="1">';
			echo '</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td class="adminlist_first">';
			echo '<input type="submit" name="submit_admin_resize_button" value="go">';
			echo '</td>';
			echo '</tr>';
			echo '</table>';
			echo '</div>';
			echo '<input type="hidden" name="newpath" value="'.$path_for_link.'">';
			echo '<input type="hidden" name="file" value="'.$_REQUEST['file'].'">';
			echo '<input type="hidden" name="action" value="resizing">';
			echo '</form>';
		}
	}
} // end admin

echo '</body>';
echo '</html>';
?>