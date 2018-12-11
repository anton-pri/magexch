{$lng.txt_test_https_module_descr}

<a href="{$catalogs.admin}/index.php?target=configuration" title="{$lng.lbl_general_settings|escape}">{$lng.lbl_click_here_to_change} &gt;&gt;</a>
<br /><br />

{capture name=section}

<form action="index.php?target=general">
<input type="hidden" name="action" value="test_https_module" />

<table>

<tr>
	<td>{$lng.lbl_url}</td>
	<td>
	<input type="text" name="url" value="{$url}" size="60" />
	&nbsp;
	<input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}" />
	</td>
</tr>

</table>
</form>

{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_https_module_test_params extra='width="100%"'}
<br /><br />
{if $headers_data ne "" or $response_data ne ""}
{capture name=section}
<pre>
{$headers_data|escape}
<hr />
{$response_data|escape}
</pre>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_https_module_response extra='width="100%"'}
{/if}
