{**
 * viewpic.tpl -- smarty template for image viewing
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

	<tr>
		<td align="center" class="infotd">
			Showing Picture <b>{$vCurrentPictureFilenumber}</b> of <b>{$vCurrentPictureFilecount}</b>: <b>{$arrCurrentPicture.name}</b> at <b>{$arrCurrentPicture.resized_width}&nbsp;x&nbsp;{$arrCurrentPicture.resized_height}</b>
		</td>
	</tr>

	<tr>
		<td class="maintd" align="center">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="third" align="left" valign="top">
						<table border="0">
							<tr>
								<td valign="bottom">
								{if $arrPrevPicture.href != ""}
									<div class="iconbg" style="width:{math equation="x + y" x=$arrPrevPicture.resized_width y=37}px;">
										<div class="icon" onclick="window.location.href='{$arrPrevPicture.href}';" onmouseover="this.style.borderColor='#81B61B';this.style.cursor='hand';" onmouseout="this.style.borderColor='#FAFAFA';this.style.backgroundColor='#EAEAEA'">
											<a href="{$arrPrevPicture.href}"><img onmouseover="return escape('&lt;b&gt;{$arrPrevPicture.name}&lt;/b&gt;&lt;br/&gt;&lt;br/&gt;Created: &lt;b&gt;{$arrPrevPicture.date}&lt;/b&gt;&lt;br/&gt;Size: &lt;b&gt;{$arrPrevPicture.size}&lt;/b&gt;')" src="{$arrPrevPicture.img}" alt="{$arrPrevPicture.name}" class="border" width="{$arrPrevPicture.resized_width}" height="{$arrPrevPicture.resized_height}"/></a>
										</div>
									</div>
								{else}
									<div class="iconbg2" style="width:{math equation="x + y" x=$arrNextPicture.resized_width y=37}px;">
										&nbsp;
									</div>
								{/if}
								</td>
							</tr>
							<tr>
							{if $arrPrevPicture.href != ""}
								<td valign="top" align="center">
									<a href="{$arrPrevPicture.href}" title="{$arrPrevPicture.name}">&lt; previous</a>
								</td>
							{else}
								&nbsp;
							{/if}
							</tr>
						</table>
					</td>
					<td align="center">
						<img src="{$arrCurrentPicture.img}" class="border" width="{$arrCurrentPicture.resized_width}" height="{$arrCurrentPicture.resized_height}" alt="{$arrCurrentPicture.name}"/>
						{if $arrCurrentPicture.description}
						<br/><br/>
						<div class="description">{$arrCurrentPicture.description}</div>
						{/if}
					</td>
					<td class="third" align="right" valign="top">
						<table border="0">
							<tr>
								<td valign="bottom">
								{if $arrNextPicture.href != ""}
									<div class="iconbg2" style="width:{math equation="x + y" x=$arrNextPicture.resized_width y=37}px;">
										<div class="icon" onclick="window.location.href='{$arrNextPicture.href}';" onmouseover="this.style.borderColor='#81B61B';this.style.cursor='hand';" onmouseout="this.style.borderColor='#FAFAFA';this.style.backgroundColor='#EAEAEA'">
											<a href="{$arrNextPicture.href}"><img onmouseover="return escape('&lt;b&gt;{$arrNextPicture.name}&lt;/b&gt;&lt;br/&gt;&lt;br/&gt;Created: &lt;b&gt;{$arrNextPicture.date}&lt;/b&gt;&lt;br/&gt;Size: &lt;b&gt;{$arrNextPicture.size}&lt;/b&gt;')" src="{$arrNextPicture.img}" alt="{$arrNextPicture.name}" class="border" width="{$arrNextPicture.resized_width}" height="{$arrNextPicture.resized_height}"/></a>
										</div>
									</div>
								{else}
									<div class="iconbg2" style="width:{math equation="x + y" x=$arrPrevPicture.resized_width y=37}px;">
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
		<td align="center" class="infotd_bottom">
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
			<b>Picture info:</b><br/>Name: <b>{$arrCurrentPicture.name}</b><br/>Original size: <b>{$arrCurrentPicture.info.width}&nbsp;x&nbsp;{$arrCurrentPicture.info.height}</b><br/>Created: <b>{$arrCurrentPicture.info.date}</b><br/>Filesize: <b>{$arrCurrentPicture.info.filesize}</b><br/>Processing Time: <b>{$vProcessingTime}&nbsp;sec.</b>
		</td>
	</tr>
{/strip}
{* end of template *}