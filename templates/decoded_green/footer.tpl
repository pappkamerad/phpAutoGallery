{**
 * footer.tpl -- smarty template for page footer
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

{if $arrTemplateConfig.show_admin_link}
<tr>
	<td align="center">
		[ <a href="javascript:;" onclick="openWindow('__phpAutoGallery__phpLoader/admin.php', 'phpAutoGallery_admin');">admin</a> ]
	</td>
</tr>
{/if}

<tr>
	<td align="center">
		Indexed&nbsp;by&nbsp;<a href="http://phpautogallery.sourceforge.net">phpAutoGallery</a>&nbsp;v{$vVersion}<br/>{$vCopyright}
	</td>
</tr>
</table>

<script src="{$vJavascriptPath}/wz_tooltip.js" type="text/javascript"></script>

</body>

</html>

{/strip}
{* end of template *}