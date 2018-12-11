{include file='common/page_title.tpl' title=$lng.lbl_froogle_export}

{$lng.txt_froogle_note}

<br /><br />

<!-- IN THIS SECTION -->



<!-- IN THIS SECTION -->

<br />

{capture name=section}
{$lng.txt_froogle_format_note}
<br />
<br />

<form action="index.php?target=froogle" method="post" name="froogleform">
<input type="hidden" name="action" value="fcreate" />
<table cellspacing="5" cellpadding="0">

<tr>
    <td style="padding-bottom: 5px;">{$lng.lbl_froogle_select_language}</td>
    <td>
{if $all_languages_cnt gt 1}
<select name="froogle_lng">
{foreach from=$all_languages item=l}
	<option value="{$l.code}"{if $froogle_lng eq $l.code} selected="selected"{/if}>{$l.language}</option>
{/foreach}
</select>
{else}
{$all_languages.0.language}
{/if}
	</td>
</tr>
<tr>
	<td width="50%" style="padding-bottom: 5px;">{$lng.lbl_froogle_enter_language_code}</td>
	<td><input type="text" name="froogle_iso" value="{$froogle_iso|default:"eng"}" maxlength="3" size="3" /></td>
</tr>

<tr>
	<td>{$lng.lbl_filename}</td>
	<td><input type="text" name="froogle_file" value="{$froogle_file|default:$def_froogle_file}" size="25" /></td>
</tr>
<tr>
	<td colspan="2" class="SubmitBox">
	<input type="button" value="{$lng.lbl_export|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, 'fcreate');" />
	<input type="button" value="{$lng.lbl_download|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, 'fdownload');" />
	{if $is_ftp_module eq 'Y'}
	<input type="button" value="{$lng.lbl_upload|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, 'fupload');" />
	{/if}
</td>
</tr>
</table>
</form>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_froogle_export content=$smarty.capture.section extra='width="100%"'}

