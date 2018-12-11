{capture name=section}

{if $smarty.get.mode neq 'add'}
{capture name=block2}

<form action="index.php?target={$current_target}" method="post" name="domains_modify_form">
<input type="hidden" name="action" value="update" />
<div class="box">
<table class="table table-striped dataTable" width="100%">

{if $domains}
{foreach from=$domains item=domain}
<thead>
<tr>
    <th colspan="3" align="center">{$domain.name|default:"Domain name"}</th>
</tr>
</thead>

<tbody class="domain_box">
<tr>
    {*<th><input type='checkbox' class='select_all' class_to_select='domains_item' /></th>*}
    <th>{$lng.lbl_title}</th>
    <th>{$lng.lbl_http_host}</th>
    <th>{$lng.lbl_https_host}</th>

</tr>
<tr{*cycle values=", class='cycle'"*} valign="top">
    {*<td align="center"><input type="checkbox" name="posted_data[{$domain.domain_id}][del]" value="1" class="domains_item" /></td>*}
    <td>
        <input type="text" class="form-control" name="posted_data[{$domain.domain_id}][name]" value="{$domain.name|escape}" {edit_on_place table="domains" pk=$domain.domain_id field="name"} /><br/>

    </td>
    <td>
    	<input type="text" class="form-control" name="posted_data[{$domain.domain_id}][http_host]" value="{$domain.http_host|escape}" {edit_on_place table="domains" pk=$domain.domain_id field="http_host"}/><br />
        {$lng.lbl_alias}:<br />
    	<textarea class="form-control" name="posted_data[{$domain.domain_id}][http_alias_hosts]" rows="3" cols="5" style="height: 50px;" {edit_on_place table="domains" pk=$domain.domain_id field="http_alias_hosts"} >{$domain.http_alias_hosts|escape}</textarea>
    </td>
    <td><input type="text" class="form-control" name="posted_data[{$domain.domain_id}][https_host]" value="{$domain.https_host|escape}" {edit_on_place table="domains" pk=$domain.domain_id field="https_host"} /></td>

</tr>

<tr>
    <th>{$lng.lbl_web_dir}</th>
    <th>{$lng.lbl_skin}</th>
    <th>{$lng.lbl_language}</th>
</tr>

<tr valign="top">
    <td><input type="text" class="form-control" name="posted_data[{$domain.domain_id}][web_dir]" value="{$domain.web_dir|escape}" {edit_on_place table="domains" pk=$domain.domain_id field="web_dir"} /></td>
    <td><input type="text" class="form-control" name="posted_data[{$domain.domain_id}][skin]" value="{$domain.skin|escape}" size="10" {edit_on_place table="domains" pk=$domain.domain_id field="skin"} /></td>
    <td>
        {include file='admin/select/language.tpl' name="posted_data[`$domain.domain_id`][language]" value=$domain.language}<br/><br/>
        {include file='admin/select/language.tpl' name="posted_data[`$domain.domain_id`][languages][]" value=$domain.languages multiple=5}
    </td>
</tr>

<tr>
	<td align="left" colspan="3" class="mobile_cell">
		<div class="form-horizontal">
			{include file='admin/attributes/object_modify.tpl' attributes=$domain.attributes}
		</div>
	</td>
</tr>

<tr>
    <td colspan="3" class="form-horizontal">
      <div class="form-group">
        <label class="col-xs-12">{$lng.lbl_actions}</label>
        <div class="col-xs-12">
        <a href="index.php?target={$current_target}&mode=config&domain_id={$domain.domain_id}" class="mdm-settings">{$lng.lbl_edit_config}</a>
        {if $domain.skin ne $default_skin}
        <a href="index.php?target={$current_target}&action=copy_basic&domain_id={$domain.domain_id}" class="mdm-settings">{$lng.lbl_mdm_copy_basic_skin}</a>
        <a href="index.php?target={$current_target}&action=cleanup&domain_id={$domain.domain_id}" class="mdm-settings">{$lng.lbl_mdm_compare_cleanup}</a>
        {/if}
        </div>
      </div>
    </td>
</tr>
<tr>
    <td colspan="3">
      <div>
        <label>{$lng.lbl_delete} <input type="checkbox" name="posted_data[{$domain.domain_id}][del]" value="1" class="domains_item" /></label>
        
      </div>
    </td>
</tr>

</tbody>
{/foreach}
{else}
<tr>
    <td colspan="3" align="center">{$lng.lbl_no_domains}</td>
</tr>
{/if}

<tbody>

</tbody>

</table>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('domains_modify_form');" style="btn-green push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: if (confirm('`$lng.lbl_domain_delete_confirmation`')) cw_submit_form('domains_modify_form', 'delete');"  style="btn-danger push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=domains&mode=add" style="btn-green push-5-r push-20"}
</div>
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2}

{else}
{capture name=block}
<form action="index.php?target={$current_target}" method="post" name="domains_modify_form">
<input type="hidden" name="action" value="update" />
<div class="box">
<table class="table table-striped dataTable" width="100%">
<thead>
<tr>
    <th colspan="3" align="center">{$lng.lbl_add_new}</th>
</tr>
</thead>
<thead>

<tr>
    <th>{$lng.lbl_title}</th>
    <th>{$lng.lbl_http_host}</th>
    <th>{$lng.lbl_https_host}</th>
</tr>
</thead>

<tbody class="domain_box">


<tr valign="top">
    <td><input type="text" class="form-control" name="posted_data[0][name]" value="" /></td>
    <td>
        <input type="text" class="form-control" name="posted_data[0][http_host]" value="" /><br />
        {$lng.lbl_alias}:<br />
        <textarea name="posted_data[0][http_alias_hosts]" class="form-control" rows="3" cols="5" style="height: 50px;"></textarea>
    </td>
    <td><input type="text" class="form-control" name="posted_data[0][https_host]" value="" /></td>

</tr>
<thead>
<tr>
    <th>{$lng.lbl_web_dir}</th>
    <th>{$lng.lbl_skin}</th>
    <th>{$lng.lbl_language}</th>
</tr>
</thead>

<tr valign="top" >
    <td><input type="text" class="form-control" name="posted_data[0][web_dir]" value="" /></td>
    <td><input type="text" class="form-control" name="posted_data[0][skin]" value="" /></td>
    <td>
        {include file='admin/select/language.tpl' name="posted_data[0][language]" value=''}<br/><br/>
        {include file='admin/select/language.tpl' name="posted_data[0][languages]" values='' multiple=5}
    </td>
</tr>
<tr>
    <td align="left" colspan="3" class="mobile_cell">
    	<div class="form-horizontal">
        	{include file='admin/attributes/object_modify.tpl'}
        </div>
    </td>
</tr>
</tbody>

</table>
<div class="buttons">
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_add href="javascript: cw_submit_form('domains_modify_form', 'add');" style="btn-green push-20"}
</div>
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/if}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_domains}
