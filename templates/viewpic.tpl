{**
 * viewpic.tpl -- smarty template for image viewing
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
	<tr>
		<td align="center" class="infotd">
			Showing Picture <b>{$vCurrentPictureFilenumber}</b> of <b>{$vCurrentPictureFilecount}</b>: <b>{$arrCurrentPicture.name}</b> at <b>{$arrCurrentPicture.resized_width} x {$arrCurrentPicture.resized_height}</b>
		</td>
	</tr>

	<tr>
		<td class="maintd" align="center">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
				<tr>
					<td align="center" valign="top">
						<table border="0">
							<tr>
								<td valign="bottom">
								{if $arrPrevPicture.href != ""}
									<div class="iconbg" style="width:{math equation="x + y" x=$arrPrevPicture.resized_width y=37};px">
										<div class="icon" onclick="window.location.href='{$arrPrevPicture.href}';" onmouseover="this.style.borderColor='#81B61B';this.style.cursor='hand';" onmouseout="this.style.borderColor='#FAFAFA';this.style.backgroundColor='#EAEAEA'">
											<a href="{$arrPrevPicture.href}"><img src="{$arrPrevPicture.img}" alt="{$arrPrevPicture.name}" class="border" width="{$arrPrevPicture.resized_width}" height="{$arrPrevPicture.resized_height}"></a>
										</div>
									</div>
								{else}
									<div class="iconbg2" style="width:{math equation="x + y" x=$arrNextPicture.resized_width y=37};px">
										&nbsp;
									</div>
								{/if}
								</td>
							</tr>
							<tr>
							{if $arrPrevPicture.href != ""}
								<td valign="top" align="center">
									<a href="{$arrNextPicture.href}" title="{$arrNextPicture.name}">&lt; previous</a>
								</td>
							{else}
								&nbsp;
							{/if}
							</tr>
						</table>
					</td>
					<td align="center">
						<img src="{$arrCurrentPicture.img}" class="border" width="{$arrCurrentPicture.resized_width}" height="{$arrCurrentPicture.resized_height}">
					</td>
					<td align="center" valign="top">
						<table border="0">
							<tr>
								<td valign="bottom">
								{if $arrNextPicture.href != ""}
									<div class="iconbg2" style="width:{math equation="x + y" x=$arrNextPicture.resized_width y=37};px">
										<div class="icon" onclick="window.location.href='{$arrNextPicture.href}';" onmouseover="this.style.borderColor='#81B61B';this.style.cursor='hand';" onmouseout="this.style.borderColor='#FAFAFA';this.style.backgroundColor='#EAEAEA'">
											<a href="{$arrNextPicture.href}"><img src="{$arrNextPicture.img}" alt="{$arrNextPicture.name}" class="border" width="{$arrNextPicture.resized_width}" height="{$arrNextPicture.resized_height}"></a>
										</div>
									</div>
								{else}
									<div class="iconbg2" style="width:{math equation="x + y" x=$arrPrevPicture.resized_width y=37};px">
										&nbsp;
									</div>
								{/if}
								</td>
							</tr>
							<tr>
							{if $arrNextPicture.href != ""}
								<td valign="top" align="center">
									<a href="{$arrNextPicture.href}" title="{$arrNextPicture.name}">next &gt;</a>
								</td>
							{else}
								&nbsp;
							{/if}
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" class="infotd2">
			View size:&nbsp;
			{section name=i loop=$arrViewSizeLinks}
				{if $arrViewSizeLinks[i].href != ""}
					<a href="{$arrViewSizeLinks[i].href}">{$arrViewSizeLinks[i].name}</a>
				{else}
					<b>{$arrViewSizeLinks[i].name}</b>
				{/if}
				{if !$smarty.section.i.last || $arrViewOriginalLink.allowed == 1}
				&nbsp;|&nbsp;
				{/if}
			{/section}
			{if $arrViewOriginalLink.href != "" && $arrViewOriginalLink.allowed == 1}
				<a href="{$arrViewOriginalLink.href}">{$arrViewOriginalLink.name}</a>
			{elseif $arrViewOriginalLink.allowed == 1}
				<b>{$arrViewOriginalLink.name}</b>
			{/if}
		</td>
	</tr>
	
	<tr>
		<td align="center" class="infotd2">
			<b>Picture info:</b><br>Name: <b>{$arrCurrentPicture.name}</b><br>Original size: <b>{$arrCurrentPicture.info.width}&nbsp;x&nbsp;{$arrCurrentPicture.info.height}</b><br>Filesize: <b>{$arrCurrentPicture.info.filesize}</b><br>Processing Time: <b>{$vProcessingTime}&nbsp;sec.</b>
		</td>
	</tr>
{/strip}
{* end of template *}