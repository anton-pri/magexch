{include file='common/page_title.tpl' title=$lng.lbl_update_inventory}

{capture name=section}
<form method="post" action="index.php?target=inv_update" enctype="multipart/form-data">

<table cellpadding="0" cellspacing="4" width="100%">

<tr>
	<td>{$lng.lbl_update}</td>
	<td>
	<select name="what">
		 <option value="p" selected="selected">{$lng.lbl_products_prices}</option>
		 <option value="q" selected="selected">{$lng.lbl_in_stock}</option>
	</select>
	</td>
</tr>
<tr>
	<td>{$lng.lbl_csv_delimiter}</td>
	<td>{include file='main/select/delimiter.tpl'}</td>
</tr>
<tr>
	<td>{$lng.lbl_csv_file}</td>
	<td><input type="file" name="userfile" />
{if $upload_max_filesize}
<br /><font class="Star">{$lng.lbl_warning}!</font> {$lng.txt_max_file_size_that_can_be_uploaded}: {$upload_max_filesize}b.
{/if} 
	</td>
</tr>

<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_update_inventory extra='width="100%"'}
