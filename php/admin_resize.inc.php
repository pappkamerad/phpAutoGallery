<?php
if ($_REQUEST['file'] == "") {
	$current_files = getDirPictureFiles($whatpath);
}
else {
	$current_files[0] = array('name' => $_REQUEST['file']);
}

if (is_array($current_files)) {
	foreach ($current_files as $key => $file) {
		$ext = strtolower(substr($file['name'], strrpos($file['name'], '.') +1));
		$name = substr($file['name'], 0, strrpos($file['name'], '.'));
		if (getFileType($file['name']) == 1) {
			// pictures
			
			// tmp dir
			$current_tmp_path = $cfg['tmp_path'] . $cfg['tmp_pAG_path'] . $HTTP_SERVER_VARS['SERVER_NAME'] . '/' . str_replace($filesystem_root_path, "", $whatpath);
			if (!file_exists($current_tmp_path)) {
				echo "&nbsp;&nbsp;&nbsp;";
				echo "...cache folder created.<br>";
				flush();
				createTmpDirs($cfg['tmp_path'], $cfg['tmp_pAG_path'] . $HTTP_SERVER_VARS['SERVER_NAME'] . '/' . str_replace($filesystem_root_path, "", $whatpath));
			}
			
			echo '&nbsp;&nbsp;&nbsp;';
			echo 'processing: <b>'.utf8_encode($file['name']).'</b><br>';
			flush();
			
			// thumb:
			if (in_array("-1", $_POST['what'])) {
				$tmpfilename = 't' . $cfg['thumb_size'] . '_' . $name . '.jpg';
				if (!file_exists($current_tmp_path . $tmpfilename)) {
					// file doesnot exist
					echo '<a class="dummy_link" name="line'.$i++.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
					echo '- creating thumbnail ('.utf8_encode($tmpfilename).')...';
					echo '<script language="JavaScript">scrolldown('.($i - 1).');</script>';
					flush();
					$image = new Image_Toolbox($whatpath . $file['name']);
					$image->newOutputSize((integer)$cfg['thumb_size'], 0, false, true);
					$image->setResizeMethod($cfg['thumbnail_resize_method']);
					$image->save($current_tmp_path . $tmpfilename, 'jpg', $cfg['jpeg_quality']);
					@chmod($current_tmp_path . $tmpfilename, 0777);
					unset($image);
					echo '&nbsp;<b>done</b><br>';
					flush();
				}
				else {
					// file exsists:
					if ($_POST['method'] == '2') {
						echo '<a class="dummy_link" name="line'.$i++.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
						echo '- re-creating thumbnail ('.utf8_encode($tmpfilename).')...';
						echo '<script language="JavaScript">scrolldown('.($i - 1).');</script>';
						flush();
						$image = new Image_Toolbox($whatpath . $file['name']);
						$image->newOutputSize((integer)$cfg['thumb_size'], 0, false, true);
						$image->setResizeMethod($cfg['thumbnail_resize_method']);
						$image->save($current_tmp_path . $tmpfilename, 'jpg', $cfg['jpeg_quality']);
						@chmod($current_tmp_path . $tmpfilename, 0777);
						unset($image);
						echo '&nbsp;<b>done</b><br>';
						flush();
					}
				}
			}

			// resized pictures:
			foreach ($cfg['view_sizes'] as $index => $viewsize) {
				if (in_array($index, $_POST['what'])) {
					$tmpfilename = $viewsize . '_' . $name . '.jpg';
					if (!file_exists($current_tmp_path . $tmpfilename)) {
						// file doesnot exist
						echo '<a class="dummy_link" name="line'.$i++.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
						echo '- creating '.$viewsize.'px version ('.utf8_encode($tmpfilename).')...';
						echo '<script language="JavaScript">scrolldown('.($i - 1).');</script>';
						flush();
						$image = new Image_Toolbox($whatpath . $file['name']);
						$image->newOutputSize((integer)$viewsize, 0, false, false);
						if ($cfg['logo_image'] != '') {
							if (file_exists($filesystem_pAG_path_abs . '__phpAutoGallery/img/' . $cfg['logo_image'])) {
								$image->addImage($filesystem_pAG_path_abs . '__phpAutoGallery/img/' . $cfg['logo_image']);
								$image->blend($cfg['logo_position_x'], $cfg['logo_position_y']);
							}
						}
						$image->save($current_tmp_path . $tmpfilename, 'jpg', $cfg['jpeg_quality']);
						@chmod($current_tmp_path . $tmpfilename, 0777);
						unset($image);
						echo '&nbsp;<b>done</b><br>';
						flush();
					}
					else {
						// file exsists:
						if ($_POST['method'] == '2') {
							echo '<a class="dummy_link" name="line'.$i++.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
							echo '- re-creating '.$viewsize.'px version ('.utf8_encode($tmpfilename).')...';
							echo '<script language="JavaScript">scrolldown('.($i - 1).');</script>';
							flush();
							$image = new Image_Toolbox($whatpath . $file['name']);
							$image->newOutputSize($viewsize, 0, false, false);
							if ($cfg['logo_image'] != '') {
								if (file_exists($filesystem_pAG_path_abs . '__phpAutoGallery/img/' . $cfg['logo_image'])) {
									$image->addImage($filesystem_pAG_path_abs . '__phpAutoGallery/img/' . $cfg['logo_image']);
									$image->blend($cfg['logo_position_x'], $cfg['logo_position_y']);
								}
							}
							$image->save($current_tmp_path . $tmpfilename, 'jpg', $cfg['jpeg_quality']);
							@chmod($current_tmp_path . $tmpfilename, 0777);
							unset($image);
							echo '&nbsp;<b>done</b><br>';
							flush();
						}
					}
				}
			}
		} // end if picture
	} // end foreach
} // end if array
else {
	echo '<a class="dummy_link" name="line'.$i++.'">&nbsp;&nbsp;&nbsp;</a>';
	echo "...nothing to do in this folder<br>";
	echo '<script language="JavaScript">scrolldown('.($i - 1).');</script>';
	flush();
}
?>