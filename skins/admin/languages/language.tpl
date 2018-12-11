{capture name=section}
<form method="post" action="index.php?target={$current_target}&language={$language}" name="topic_form">
<input type="hidden" name="action" value="update_filter" />
{capture name=block}

<table class="table table-striped dataTable vertical-center">
<tr>
    <td id="label">{$lng.lbl_select_topic}:</td>
    <td>
        <select name="posted_data[topic]" style="width:auto;" class="form-control">
	    <option value=""{if $search_prefilled.topic eq ""} selected="selected"{/if}>{$lng.lbl_all}</option>
        {foreach from=$topics item=t}
	    <option value="{$t}"{if $search_prefilled.topic eq $t} selected="selected"{/if}>{$t}</option>
        {/foreach}
        </select>
    </td>
</tr>
<tr>
    <td id="label">{$lng.lbl_apply_filter}:</td>
    <td><input class="form-control" type="text" size="16" name="posted_data[filter]" value="{$search_prefilled.filter|escape:"html"}" /></td>
</tr>
<tr>
    <td id="label">{$lng.lbl_show_only_not_translated}:</td>
    <td><input type="checkbox" size="16" name="posted_data[not_translated]" value="1" {if $search_prefilled.not_translated}checked{/if} /></td>
</tr>
</table>
<div class="buttons">{include file='admin/buttons/button.tpl' href="javascript:document.topic_form.submit();" button_title=$lng.lbl_search view="top_" style="btn-green push-20"}</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block title=$lng.lbl_search}


</form>



{if $data}
{capture name=block2}
<div class="row">
	<div class="col-xs-6 left-align">{include file='common/navigation_counter.tpl'}</div>
	<div class="col-xs-6 left-align">{include file='common/navigation.tpl'}</div>
</div>
<form action="index.php?target={$current_target}&language={$language}" method="post" name="languages_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />

{assign var='current_topic' value=''}
<div class="form-horizontal">

<div class="form-group">
    <div class="col-xs-4" style="font-weight: bold;">{$lng.lbl_name}</div>
    <div class="col-xs-4" style="font-weight: bold;">{$lng.lbl_value}</div>
    <div class="col-xs-4" style="font-weight: bold;">{$lng.lbl_tooltip}</div>
</div>

{foreach from=$data item=lbl}
{if $lbl.topic ne $current_topic}
    {if $current_topic ne ''}
{include file='common/subheader.tpl' title="`$lng.lbl_topic`: `$lbl.topic`"}
{assign var='current_topic' value=$lbl.topic}
    {/if}
{/if}

<div class="form-group">
<div class="col-xs-4">
    <label><input type="checkbox" name="ids[]" value="{$lbl.name}" /> {$lbl.name}</label>
</div>
<div class="col-xs-4">
    <textarea name="var_value[{$lbl.name}][name]" cols=40 rows=8 class="form-control">{$lbl.value}</textarea>
</div>
<div class="col-xs-4">
    <textarea name="var_value[{$lbl.name}][tooltip]" cols=30 rows=8 class="form-control">{$lbl.tooltip}</textarea>
</div>
	{*include file='main/textarea.tpl' name="var_value[`$lbl.name`]" data=$lbl.value init_mode='exact'*}
</div>
{/foreach}
</div>

<div class="row">
	<div class="col-xs-12">{include file='common/navigation.tpl'}</div>
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update_all href="javascript:cw_submit_form(document.languages_form)" style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript:cw_submit_form(document.languages_form, 'delete')" style="btn-danger push-20 push-5-r"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2 title=$lng.lbl_search_results}
{/if}

{capture name=block3}
<form action="index.php?target={$current_target}" method="post" name="add_form">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="language" value="{$smarty.get.language|escape:"html"}" />

<div class="form-horizontal">

{*include file="common/subheader.tpl" title=$lng.lbl_add_new_entry*}
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_select_topic}</label>
	<div class="col-xs-12">
	<select name="new_topic" class="form-control">
		{foreach from=$topics item=t}
		<option value="{$t}"{if $new_topic_default eq $t} selected="selected"{/if}>{$t}</option>
		{/foreach}
	</select>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_variable}</label>
	<div class="col-xs-12">
		<input type="text" size="50" name="new_var_name" class="form-control" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_value}</label>
	<div class="col-xs-12">
    	<textarea name="new_var_value" cols=70 rows=8 data="" class="form-control"></textarea>
    </div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_tooltip}</label>
	<div class="col-xs-12">
    	<textarea name="new_var_tooltip" cols=70 rows=8 data="" class="form-control"></textarea>
    </div>
</div>
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update_all href="javascript:cw_submit_form(document.add_form)" style="btn-green push-20 push-5-r"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block3 title=$lng.lbl_add_new_entry}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_language}