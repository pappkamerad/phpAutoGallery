<?php
/**
 * functions.inc.php -- functions library for phpAutoGallery
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
 
function getArrayPosition(&$array, $searchvalue) {
	foreach ($array as $key => $value) {
		if ($value === $searchvalue) {
			return $key;
		}
	}
	return -1;
}

function filesize_human($file) {
	$bytes = filesize($file);
	$types =  Array("B","K","M","G","T");
	$current = 0;
	while ($bytes > 1024) {
 		$current++;
 		$bytes /= 1024;
	}
	return round($bytes,2) . $types[$current];
} 

function getmicrotime() {
	list($usec, $sec) = explode(" ", microtime()); 
	return ((float)$usec + (float)$sec); 
}

function getDirBytes($dir) {
	$line = @exec('du "' . $dir . '" -sbS');
	if (!$line) {
		return false;
	}
	$line2 = explode("\t", $line);
	$bytes = $line2[0];
	$types =  Array("B", "K", "M", "G", "T");
	$current = 0;
	while ($bytes > 1024) {
 		$current++;
 		$bytes /= 1024;
	}
	return round($bytes,2) . $types[$current];
}

function getDirBytesTotal($dir) {
	$line = @exec('du "' . $dir . '" -sb');
	if (!$line) {
		return false;
	}
	$line2 = explode("\t", $line);
	$bytes = $line2[0];
	$types =  Array("B", "K", "M", "G", "T");
	$current = 0;
	while ($bytes > 1024) {
 		$current++;
 		$bytes /= 1024;
	}
	return round($bytes,2) . $types[$current];
}

function getDirFiles($dirPath) {
	global $cfg;
    if (strlen($dirPath)!=(strrpos($dirPath, '/'))+1) {
    	$dirPath.='/';
    }
    if ($handle = opendir($dirPath)) {
        while (false !== ($file = readdir($handle))) {
        	if (!in_array($file, $cfg['hide_file']) && $file != "." && $file != ".." && !is_dir($dirPath.$file)) {
                	$filesArr[] = trim($file);
                }
        }
        closedir($handle);
     }  
     return $filesArr;
}

function getDirDirs($dirPath) {
	global $cfg;
    if (strlen($dirPath)!=(strrpos($dirPath, '/'))+1) {
    	$dirPath.='/';
    }
    if ($handle = opendir($dirPath)) {
        while (false !== ($file = readdir($handle))) {
        	if (!in_array($file, $cfg['hide_folder']) && $file != "." && $file != ".." && $file != '__phpAutoGallery' && is_dir($dirPath.$file)) {
                	$dirArr[] = trim($file);
                }
        }
        closedir($handle);
     }
     return $dirArr;
}

function getDirTree($dirPath, &$dirTree, $level = 0, $hrefPath = "") {
	global $cfg, $web_pAG_path_rel;
	if (strlen($dirPath)!=(strrpos($dirPath, '/'))+1) {
    	$dirPath.='/';
    }
    if ($handle = opendir($dirPath)) {
        while (false !== ($file = readdir($handle))) {
        	if (!in_array($file, $cfg['hide_folder']) && $file != "." && $file != ".." && $file != '__phpAutoGallery' && is_dir($dirPath.$file)) {
                $name_prefix = "";
                for ($i = 0; $i < $level; $i++) {
                	$name_prefix .= "-";
                }
                $dirTree[] = array("prefix" => $name_prefix, "level" => $level, "name" => trim($file), "href" => $web_pAG_path_rel . $hrefPath . trim($file));
                getDirTree($dirPath . trim($file), $dirTree, $level + 1, $hrefPath . trim($file) . '/');
			}
        }
        closedir($handle);
     }
}

function getDirPictureFiles($dirPath) {
	global $cfg;
	if (strlen($dirPath)!=(strrpos($dirPath, '/'))+1) {
    	$dirPath.='/';
    }
    if ($handle = opendir($dirPath)) {
    	while (false !== ($file = readdir($handle))) {
        	if (!in_array($file, $cfg['hide_file']) && $file != "." && $file != ".." && $file != '__phpAutoGallery' && !is_dir($dirPath.$file)) {
        		$file = trim($file);
        		$ext = strtolower(substr($file, strrpos($file, '.') +1));
				if ($cfg['types'][$ext] == 1) {
					$filesArr[] = $file;	
				}
        	}
        }
		closedir($handle);
	}  
	return $filesArr;
}


function createTmpDirs($tmproot, $fullpath) {
	if ($fullpath != '') {
		$dirs = explode('/', $fullpath);
		for($i = 0; $dirs[$i] !== ''; $i++) {
			$absdir = $tmproot;
			for($u = 0; $u <= $i; $u++) {
				$absdir .= $dirs[$u];
				if (($u + 1) <= $i) {
					$absdir .= '/';
				}
			}
			if(!file_exists($absdir)) {
				mkdir($absdir, 0777);
				@chmod($absdir, 0777);
			}
		}
	}
}
?>