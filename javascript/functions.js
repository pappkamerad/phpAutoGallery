/**
 * functions.js -- javascript functions
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

function Go(x) {
 if(x == "nothing") {
   document.forms[0].reset();
   document.forms[0].elements[0].blur();
   return;
 }
 else if(x == "end")
   top.location.href = parent.frames[1].location;
 else {
   top.location.href = x;
   document.forms[0].reset();
   document.forms[0].elements[0].blur();
 }
}

function openWindow(win_url, win_name) {
	window.open(win_url + '?path=' + escape(window.location), win_name, 'menubar=no,width=640,height=450,left=200,top=300,scrollbars=yes');
}

function scrolldown(i) {
	if (navigator.appName == "Microsoft Internet Explorer") {
		window.location.href = "#line" + i;
	}
}