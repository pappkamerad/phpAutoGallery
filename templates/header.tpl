{**
 * header.tpl -- smarty template for page header
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
 *}
{strip}

<html>
<head>
	<title>{$vGalleryName}&nbsp;::&nbsp;{$vCurrentRequest}</title>
	<link rel="stylesheet" type="text/css" href="{$vRootPath}__phpAutoGallery__cssLoader/style.css">
</head>

<body>

<table width="98%" border="0" align="center" cellpadding="7" cellspacing="0">
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