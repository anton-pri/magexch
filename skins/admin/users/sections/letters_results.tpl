<form action="index.php?target={$current_target}&user={$user}" method="post" name="letters_form">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="delete" />
<table class="header" width="50%">
<tr>
    <th>&nbsp;</th>
    <th>{$lng.lbl_file}</th>
    <th>{$lng.lbl_date}</th>
    <th>{$lng.lbl_uploaded_by}</th>
</tr>
{if $letters}
{foreach from=$letters item=letter}
<tr>
    <td><input type="checkbox" name="del[{$letter.file_id}]" value="1"></td>
    <td><a href="{$letter.file_url}">{$letter.filename}</a></td>
    <td>{$letter.date|date_format:$config.Appearance.datetime_format}</td>
    <td>{$letter.uploaded_by}</td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="4" align="center">{$lng.lbl_not_found}</td>
{/if}
</table>
{if $letters}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('letters_form', 'delete');"}
{/if}
</form>

{include file="common/subheader.tpl" title=$lng.lbl_add_new}
<form action="index.php?target={$current_target}&mode={$mode}&user={$user}" method="post" name="upload_letter_form" enctype="multipart/form-data">
<input type="hidden" name="action" value="upload" />

<img id="edit_customer_image" src="{$app_web_dir}/index.php?target=image&id=0&amp;type=customers_images&amp;tmp" alt="" />

<div class="input_field_0">
    <label>{$lng.lbl_select_file}:</label>
    <input type="file" name="userfile" />
{include file='buttons/button.tpl' button_title=$lng.lbl_upload href="javascript:cw_submit_form('upload_letter_form');"}

</form>

