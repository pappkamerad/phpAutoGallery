{**
 * header.tpl -- smarty template for page header
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
 *}
{strip}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title>{$vGalleryName}&nbsp;::&nbsp;{$vCurrentRequest}</title>
	<link rel="stylesheet" type="text/css" href="{$vRootPath}__phpAutoGallery__cssLoader/style.css"/>
	<script src="{$vRootPath}__phpAutoGallery__jsLoader/functions.js" type="text/javascript"></script>
</head>

<body>

<table width="98%" cellpadding="7" cellspacing="0">
	<tr>
		<td align="center" class="headertd">
{section name=i loop=$arrCurrentNav}
	{if $arrCurrentNav[i].href}
		<a href="{$arrCurrentNav[i].href}">{$arrCurrentNav[i].name}</a>
	{else}
		<b>{$arrCurrentNav[i].name}</b>
	{/if}
	{if !$smarty.section.i.last}
		&nbsp;/&nbsp;
	{/if}
{/section}
		</td>
	</tr>

{/strip}
{* end of template *}