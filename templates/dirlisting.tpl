{**
 * dirlisting.tpl -- smarty template for directory listing
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
					<td width="33%" align="left">
						{if $vViewPrev != ''}
						<a href="{$vViewPrev}">&lt; previous page</a>
						{else}
						&nbsp;
						{/if}
					</td>
					<td width="33%" align="center">
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
					<td width="33%" align="right">
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
					<div class="iconbgvideo">
						<div class="iconvideo" onclick="window.location.href='{$arrCurrentDirDirs[i].href}'" onmouseover="this.style.borderColor='#81B61B';this.style.backgroundColor='#F2F2F2';this.style.cursor='hand';" onmouseout="this.style.borderColor='#FAFAFA';this.style.backgroundColor='#FAFAFA';">
							<a href="{$arrCurrentDirDirs[i].href}"><img src="{$arrCurrentDirDirs[i].img}" alt="{$arrCurrentDirDirs[i].name}" class="noborder" width="48" height="48"/><br/>{$arrCurrentDirDirs[i].name}</a>
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
							<a href="{$arrCurrentDirFiles[i].href}" title="{$arrCurrentDirFiles[i].name}"><img src="{$arrCurrentDirFiles[i].img}" alt="{$arrCurrentDirFiles[i].name}" class="border" width="{$arrCurrentDirFiles[i].resized_width}" height="{$arrCurrentDirFiles[i].resized_height}"/></a>
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
					<div class="iconbgvideo">
						<div class="iconvideo" onclick="window.location.href='{$arrCurrentDirFiles[i].href}'" onmouseover="this.style.borderColor='#81B61B';this.style.backgroundColor='#F2F2F2';this.style.cursor='hand';" onmouseout="this.style.borderColor='#FAFAFA';this.style.backgroundColor='#FAFAFA';">
							<a href="{$arrCurrentDirFiles[i].href}" title="{$arrCurrentDirFiles[i].name}"><img src="{$arrCurrentDirFiles[i].img}" class="noborder" alt="{$arrCurrentDirFiles[i].name}"/><br/>{$arrCurrentDirFiles[i].name}</a>
						</div>
					</div>
				{/if}
			{/section}
		</td>
		</tr>
		</table>
		</td>		
	</tr>
	
	{if $arrViewPages != ''}
	<tr>
		<td align="center" class="infotd2">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="33%" align="left">
						{if $vViewPrev != ''}
						<a href="{$vViewPrev}">&lt; previous page</a>
						{else}
						&nbsp;
						{/if}
					</td>
					<td width="33%" align="center">
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
					<td width="33%" align="right">
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
		<td align="center" class="infotd2">
			<b>Directory info:</b><br/>Name: <b>{$vCurrentDirName}</b><br/>Size: <b>{$vCurrentDirBytecount}</b> / <b>{$vCurrentDirBytecountTotal}</b><br/>Subdirectories: <b>{$vCurrentDirDircount}</b><br/>Pictures: <b>{$arrCurrentDirFilecount[1]}</b><br/>Videos: <b>{$arrCurrentDirFilecount[2]}</b><br/>Processing Time: <b>{$vProcessingTime}&nbsp;sec.</b>
		</td>
	</tr>

{/strip}
{* end of template *}