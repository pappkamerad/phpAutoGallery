{**
 * dirlisting.tpl -- smarty template for directory listing
 * template: decoded_green
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
 *}
{strip}

{if $arrCurrentDirFilecount[1] != 0}
	<tr>
		<td align="center" class="infotd">
						Showing: <b>{$vCurrentDirStartPic}</b> - <b>{$vCurrentDirEndPic}</b> of <b>{$arrCurrentDirFilecount[1]}</b> Pictures
		</td>
	</tr>
{/if}
	
{if $arrViewPages != ''}
	<tr>
		<td align="center" class="infotd">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="width_33" align="left">
						{if $vViewPrev != ''}
						<a href="{$vViewPrev}">&lt; previous page</a>
						{else}
						&nbsp;
						{/if}
					</td>
					<td class="width_33" align="center">
						View page:&nbsp;
						{section name=i loop=$arrViewPages}
						{if $arrViewPages[i].href != ''}
						<a href="{$arrViewPages[i].href}">{$arrViewPages[i].name}</a>
						{else}
						<b>{$arrViewPages[i].name}</b>
						{/if}
						{if !$smarty.section.i.last}
						&nbsp;|&nbsp;
						{/if}
						{/section}
					</td>
					<td class="width_33" align="right">
						{if $vViewNext != ''}
						<a href="{$vViewNext}">next page &gt;</a>
						{else}
						&nbsp;
						{/if}
					</td>
				</tr>
			</table>
		</td>
	</tr>
{/if}

	<tr>
		<td class="maintd">
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
		<td>
			{* directories *}
			{section name=i loop=$arrCurrentDirDirs}
					<div class="iconbgvideo" onmouseover="return escape('&lt;b&gt;{$arrCurrentDirDirs[i].name}&lt;/b&gt;&lt;br/&gt;&lt;br/&gt;Created: &lt;b&gt;{$arrCurrentDirDirs[i].date}&lt;/b&gt;&lt;br/&gt;Size: &lt;b&gt;{$arrCurrentDirDirs[i].size}&lt;/b&gt; / &lt;b&gt;{$arrCurrentDirDirs[i].totalsize}&lt;/b&gt;&lt;br/&gt;Subdirectories: &lt;b&gt;{$arrCurrentDirDirs[i].dirs}&lt;/b&gt; / &lt;b&gt;{$arrCurrentDirDirs[i].totaldirs}&lt;/b&gt;&lt;br/&gt;Pictures: &lt;b&gt;{$arrCurrentDirDirs[i].pictures}&lt;/b&gt; / &lt;b&gt;{$arrCurrentDirDirs[i].totalpictures}&lt;/b&gt;&lt;br/&gt;Videos: &lt;b&gt;{$arrCurrentDirDirs[i].videos}&lt;/b&gt; / &lt;b&gt;{$arrCurrentDirDirs[i].totalvideos}&lt;/b&gt;')">
						<div class="iconvideo" onclick="window.location.href='{$arrCurrentDirDirs[i].href}'" onmouseover="this.style.borderColor='#81B61B';this.style.backgroundColor='#F2F2F2';this.style.cursor='hand';" onmouseout="this.style.borderColor='#FAFAFA';this.style.backgroundColor='#FAFAFA';">
							<a href="{$arrCurrentDirDirs[i].href}"><img src="{$arrCurrentDirDirs[i].img}" class="noborder" width="48" height="48" alt="{$arrCurrentDirDirs[i].name}" onmouseover="return escape('&lt;b&gt;{$arrCurrentDirDirs[i].name}&lt;/b&gt;&lt;br/&gt;&lt;br/&gt;Created: &lt;b&gt;{$arrCurrentDirDirs[i].date}&lt;/b&gt;&lt;br/&gt;Size: &lt;b&gt;{$arrCurrentDirDirs[i].size}&lt;/b&gt; / &lt;b&gt;{$arrCurrentDirDirs[i].totalsize}&lt;/b&gt;&lt;br/&gt;Subdirectories: &lt;b&gt;{$arrCurrentDirDirs[i].dirs}&lt;/b&gt; / &lt;b&gt;{$arrCurrentDirDirs[i].totaldirs}&lt;/b&gt;&lt;br/&gt;Pictures: &lt;b&gt;{$arrCurrentDirDirs[i].pictures}&lt;/b&gt; / &lt;b&gt;{$arrCurrentDirDirs[i].totalpictures}&lt;/b&gt;&lt;br/&gt;Videos: &lt;b&gt;{$arrCurrentDirDirs[i].videos}&lt;/b&gt; / &lt;b&gt;{$arrCurrentDirDirs[i].totalvideos}&lt;/b&gt;')"/></a><br/><a href="{$arrCurrentDirDirs[i].href}">{$arrCurrentDirDirs[i].name}</a>
						</div>
					</div>
			{/section}
		</td>
		</tr>
		<tr>
		<td>
			{* pictures *}
			{section name=i loop=$arrCurrentDirFiles}
				{if $arrCurrentDirFiles[i].type == 1}
					<div class="iconbg" style="height:{math equation="x + y" x=$arrCurrentDirFilesHighestHeight y=50}px;">
						<div class="icon" onclick="window.location.href='{$arrCurrentDirFiles[i].href}'" onmouseover="this.style.borderColor='#81B61B';this.style.cursor='hand'" onmouseout="this.style.borderColor='#FAFAFA';this.style.backgroundColor='#EAEAEA'">
							<a href="{$arrCurrentDirFiles[i].href}"><img onmouseover="return escape('&lt;b&gt;{$arrCurrentDirFiles[i].name}&lt;/b&gt;&lt;br/&gt;&lt;br/&gt;Created: &lt;b&gt;{$arrCurrentDirFiles[i].date}&lt;/b&gt;&lt;br/&gt;Size: &lt;b&gt;{$arrCurrentDirFiles[i].size}&lt;/b&gt;')" src="{$arrCurrentDirFiles[i].img}" alt="{$arrCurrentDirFiles[i].name}" class="border" width="{$arrCurrentDirFiles[i].resized_width}" height="{$arrCurrentDirFiles[i].resized_height}"/></a>
						</div>
					</div>
				{/if}
			{/section}
			{if $arrCurrentDirDirs == 0 && $arrCurrentDirFilecount[1] == 0 && $arrCurrentDirFilecount[2] == 0}
				<div align="center">
					Empty directory
				</div>
			{/if}
		</td>
		</tr>
		<tr>
		<td>
			{* videos *}
			{section name=i loop=$arrCurrentDirFiles}
				{if $arrCurrentDirFiles[i].type == 2}
					<div class="iconbgvideo" onmouseover="return escape('&lt;b&gt;{$arrCurrentDirFiles[i].name}&lt;/b&gt;&lt;br/&gt;&lt;br/&gt;Created: &lt;b&gt;{$arrCurrentDirFiles[i].date}&lt;/b&gt;&lt;br/&gt;Size: &lt;b&gt;{$arrCurrentDirFiles[i].size}&lt;/b&gt;')">
						<div class="iconvideo" onclick="window.location.href='{$arrCurrentDirFiles[i].href}'" onmouseover="this.style.borderColor='#81B61B';this.style.backgroundColor='#F2F2F2';this.style.cursor='hand';" onmouseout="this.style.borderColor='#FAFAFA';this.style.backgroundColor='#FAFAFA';">
							<a href="{$arrCurrentDirFiles[i].href}"><img src="{$arrCurrentDirFiles[i].img}" class="noborder" alt="{$arrCurrentDirFiles[i].name}"/></a><br/><a href="{$arrCurrentDirFiles[i].href}">{$arrCurrentDirFiles[i].name}</a>
						</div>
					</div>
				{/if}
			{/section}
		</td>
		</tr>
		</table>
		{if $arrCurrentDirInfo.description}
		<div class="description_folder_ieworkaround">
			<div class="description_folder">{$arrCurrentDirInfo.description}</div>
		</div>
		{/if}
		</td>
	</tr>
	
	{if $arrViewPages != ''}
	<tr>
		<td align="center" class="infotd_bottom">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="width_33" align="left">
						{if $vViewPrev != ''}
						<a href="{$vViewPrev}">&lt; previous page</a>
						{else}
						&nbsp;
						{/if}
					</td>
					<td class="width_33" align="center">
						View page:&nbsp;
						{section name=i loop=$arrViewPages}
						{if $arrViewPages[i].href != ''}
						<a href="{$arrViewPages[i].href}">{$arrViewPages[i].name}</a>
						{else}
						<b>{$arrViewPages[i].name}</b>
						{/if}
						{if !$smarty.section.i.last}
						&nbsp;|&nbsp;
						{/if}
						{/section}
					</td>
					<td class="width_33" align="right">
						{if $vViewNext != ''}
						<a href="{$vViewNext}">next page &gt;</a>
						{else}
						&nbsp;
						{/if}
					</td>
				</tr>
			</table>
		</td>
	</tr>
	{/if}
	
	<tr>
	<td align="center" class="infotd_bottom">
		<form method="post" action="{$vRootPath}">
		<p>
		Jump to:&nbsp;
		<select name="quicknav" onchange="Go(this.form.quicknav.options[this.form.quicknav.options.selectedIndex].value)">
		{section name=i loop=$arrWholeTree}
			<option{if $arrWholeTree[i].active == 1} selected="selected"{/if} class="{$arrWholeTree[i].class}" value="{$arrWholeTree[i].href}">{$arrWholeTree[i].prefix}{$arrWholeTree[i].name}</option>
		{/section}
		</select>
		&nbsp;<input type="submit" name="submit_quicknav" value="go"/>
		</p>
		</form>
	</td>
	</tr>
	
	<tr>
		<td align="center" class="infotd2">
			<b>Directory info:</b><br/>Name: <b>{$arrCurrentDirInfo.name}</b><br/>Created: <b>{$arrCurrentDirInfo.date}</b><br/>Size: <b>{$arrCurrentDirInfo.size}</b> / <b>{$arrCurrentDirInfo.totalsize}</b><br/>Subdirectories: <b>{$arrCurrentDirInfo.dirs}</b> / <b>{$arrCurrentDirInfo.totaldirs}</b><br/>Pictures: <b>{$arrCurrentDirInfo.pictures}</b> / <b>{$arrCurrentDirInfo.totalpictures}</b><br/>Videos: <b>{$arrCurrentDirInfo.videos}</b> / <b>{$arrCurrentDirInfo.totalvideos}</b><br/>Processing Time: <b>{$vProcessingTime}&nbsp;sec.</b>
		</td>
	</tr>

{/strip}
{* end of template *}