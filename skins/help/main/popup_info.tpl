<table width="100%" cellpadding="0" cellspacing="0" align="center" height="{math equation="x-2" x=$force_height}">
<tr>
	<td class="PopupTitle">{$popup_title|default:"&nbsp;"}</td>
</tr>
<tr>
	<td height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
	<td class="PopupBG" height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>

<tr>
	<td height="{math equation="x-82" x=$force_height}" valign="top">
<table width="100%" cellpadding="15" cellspacing="0">
<tr>
	<td>

{include file="common/dialog_message.tpl"}

{if $template_name ne ""}
{include file=$template_name}

{elseif $pre ne ""}
{$pre}

{else}
{include file="main/error_page_not_found.tpl"}
{/if}

	</td>
</tr>
</table>
	</td>
</tr>
</table>
