{include file='common/page_title.tpl' title=$lng.lbl_test_data_encryption_title}

{$lng.txt_test_data_encryption_top_text}

<br /><br />

{capture name=section}

<form action="index.php?target=test_pgp" method="post">

<table>

<tr valign="top">
	<td>{$lng.lbl_text_to_encrypt}:</td>
	<td><textarea name="source_data" cols="60" rows="10">{$source_data|escape}</textarea></td>
</tr>

<tr>
	<td>{$lng.lbl_send_encrypted_data_to_email}</td>
	<td><input type="text" name="test_email" value="{$test_email}" /></td>
</tr>

<tr>
	<td>{$lng.lbl_show_GPG_PGP_errors}</td>
	<td><input type="checkbox" name="show_errors" value="1"{if $show_errors} checked="checked"{/if} /></td>
</tr>

<tr>
	<td colspan="2">&nbsp;</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="common/section.tpl" title=$lng.lbl_test_PGP_GPG content=$smarty.capture.section extra='width="100%"'}

{if $source_data ne ""}

<br /><br />

{capture name=section}
<pre>{$result_data|escape}</pre>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_encrypted_data content=$smarty.capture.section extra='width="100%"'}

{if $show_errors}
<br /><br />

{capture name=section}
<pre>{$error_output|escape}</pre>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_GPG_PGP_errors content=$smarty.capture.section extra='width="100%"'}
{/if}

{/if}
