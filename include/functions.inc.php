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

function is_utf8($str) {
	$eins = strlen($str);
	$zwei = strlen(utf8_decode($str));
	if (($eins - $zwei) > 0) {
		return true;
	}
	else {
		return false;
	}
}

function cp850_to_iso88592($str) {
	$map = array (
		0xC7, 0xFC, 0xE9, 0xE2, 0xE4, 0xE0, 0xE5, 0xE7, 0xEA, 0xEB, 0xE8,
		0xEF, 0xEE, 0xEC, 0xC4, 0xC5, 0xC9, 0xE6, 0xC6, 0xF4, 0xF6, 0xF2,
		0xFB, 0xF9, 0xFF, 0xD6, 0xDC, 0xF8, 0xA3, 0xD8, 0xD7, 0x9F, 0xE1,
		0xED, 0xF3, 0xFA, 0xF1, 0xD1, 0xAA, 0xBA, 0xBF, 0xAE, 0xAC, 0xBD,
		0xBC, 0xA1, 0xAB, 0xBB, 0x9B, 0x9D, 0x8D, 0x81, 0x8B, 0xC1, 0xC2,
		0xC0, 0xA9, 0x96, 0x84, 0x8C, 0x94, 0xA2, 0xA5, 0x97, 0x9C, 0x91,
		0x93, 0x80, 0x8E, 0x8F, 0xE3, 0xC3, 0x83, 0x90, 0x92, 0x85, 0x8A,
		0x99, 0x9E, 0xA4, 0xF0, 0xD0, 0xCA, 0xCB, 0xC8, 0x86, 0xCD, 0xCE,
		0xCF, 0x89, 0x82, 0x88, 0x9A, 0xA6, 0xCC, 0x98, 0xD3, 0xDF, 0xD4,
		0xD2, 0xF5, 0xD5, 0xB5, 0xFE, 0xDE, 0xDA, 0xDB, 0xD9, 0xFD, 0xDD,
		0xAF, 0xB4, 0xAD, 0xB1, 0x95, 0xBE, 0xB6, 0xA7, 0xF7, 0xB8, 0xB0,
		0xA8, 0xB7, 0xB9, 0xB3, 0xB2, 0x87, 0xA0
	);
	
	if (!is_utf8($str)) {
		for ($i=0; $i < strlen($str); $i++){
			$thischar = substr($str, $i, 1);
			$corrupt_value = ord($thischar);
			if ($corrupt_value > 127) {
				$newchar = chr($map[$corrupt_value - 128]);
			}
			else {
				$newchar = $thischar;
			}
			$str2 .= $newchar;
		}	
	}
	else {
		$str2 = $str;
	}
	return $str2;
}

function samba2workaround($str) {
	global $cfg;
	if ($cfg['samba_2_charset_workaround']) {
		return cp850_to_iso88592($str);
	}
	else {
		return $str;
	}
}

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
	global $cfg, $web_pAG_path_rel, $web_current_path;
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
                if ($level > 0) {
                	$name_prefix .= "&nbsp;";
                }
                // check if active
                $active = 0;
                if ($hrefPath . trim($file) . "/" == $web_current_path) {
                	$active = 1;
                }
                // what style
                if ($level == 0) {
                	$style_class = "bold";
                }
                else {
                	$style_class = "normal";
                }
                $dirTree[] = array("class" => $style_class, "prefix" => $name_prefix, "active" => $active, "level" => (integer)$level, "name" => utf8_encode(samba2workaround(trim($file))), "href" => utf8_encode($web_pAG_path_rel . $hrefPath . trim($file) . "/"));
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