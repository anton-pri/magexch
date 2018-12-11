

{if $manufacturer.manufacturer_id}
<div id='manufacturer_fcat'>
{capture name='section'}
<form action='index.php?target={$current_target}&manufacturer_id={$manufacturer.manufacturer_id}' method='post' name='man_cats_form'>
<input type='hidden' name='mode' />
<input type='hidden' name='action' />

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	<th width="10"><input class="select_all" type="checkbox" class_to_select='to_delete' /></th>
	<th>Name</th>
	<th>URL</th>
	<th>POS</th>
</tr>
</thead>
{foreach from=$f_categories item='fcat'}
<tr{cycle values=', class="cycle"'}>
	<td><input type='checkbox' name='fcat[{$fcat.url_id}][delete]' class='to_delete' value='1' /></td>
	<td>{$fcat.title}</td>
	<td>{$fcat.custom_facet_url}</td>
	<td><input type='text' class='micro' value='{$fcat.pos}' name='fcat[{$fcat.url_id}][pos]' /></td>
</tr>
{/foreach}
</table>

<div class="buttons">
{if $f_categories}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete_selected onclick="javascript: cw_submit_form('man_cats_form', 'delete_fcat');"}
{include file='buttons/button.tpl' button_title=$lng.lbl_update onclick="javascript: cw_submit_form('man_cats_form', 'update_fcat');"}
{/if}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section extra='width="100%"' title="Selected categories"}

{include file="common/subheader.tpl" title="Add more"}

{include file='addons/clean_urls/custom_facet_urls.tpl'}
</div>

{/if}

<div id='buttons_replacement' class="buttons">
{if $custom_facet_urls}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add onclick="javascript: cw_submit_form('custom_facet_urls_form', 'add_fcat');" style="btn-green"}
{/if}
</div>

<script>
	var manufacturer_id='{$manufacturer.manufacturer_id}';
{literal}
	$(document).ready(function(){
		$("#facet_urls_list .buttons").replaceWith($('#buttons_replacement'));
		$("form[name='custom_facet_urls_filter_form']").append("<input type='hidden' name='manufacturer_id' value='"+manufacturer_id+"' />");
		$("form[name='custom_facet_urls_form']").append("<input type='hidden' name='manufacturer_id' value='"+manufacturer_id+"' />");
		$("form[name='custom_facet_urls_form']").find('table th a').each(function(){$(this).attr('href',$(this).attr('href')+'&js_tab=fcat&manufacturer_id='+manufacturer_id)});
	});
{/literal}
</script>
