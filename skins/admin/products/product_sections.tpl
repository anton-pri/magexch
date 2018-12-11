<p>{$explanation}</p>

{assign var='form_name' value="featuredproductsform_`$featured_type`"}
<form action="index.php?target={$current_target}" method="post" name="{$form_name}">
<input type="hidden" name="action" value="update_product_section" />
<input type="hidden" name="js_tab" value="{$included_tab}" />
<input type="hidden" name="featured_type" value="{$featured_type}" />
<input type="hidden" name="cat" value="{$cat}" />

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	<th width="10"><input type='checkbox' class='select_all' class_to_select='{$form_name}_item' /></th>
	<th width="70%">{$lng.lbl_product_name}</th>
	<th width="15%" class="text-center">{$lng.lbl_pos}</th>
	<th width="15%" class="text-center">{$lng.lbl_active}</th>
</tr>
</thead>
{if $products}
{foreach from=$products item=product}
<tr{cycle values=", class='cycle'"}>
	<td width="10" align="center"><input type="checkbox" name="posted_data[{$product.product_id}][to_delete]" class="{$form_name}_item" /></td>
	<td>
        <b><a href="index.php?target=products&mode=details&product_id={$product.product_id}" target="_blank">{$product.product}</a></b>
        {if $product.from_time}({$product.from_time|date_format:$config.Appearance.date_format} - {$product.to_time|date_format:$config.Appearance.date_format}, {$lng.lbl_min_amount}: {$product.min_amount}){/if}
    </td>
	<td align="center" class="form-group"><input type="text" name="posted_data[{$product.product_id}][product_order]" size="5" value="{$product.product_order}" class="form-control" /></td>
	<td align="center" class="form-group"><input type="checkbox" name="posted_data[{$product.product_id}][avail]" value="1"{if $product.avail} checked="checked"{/if} /></td>
</tr>
{/foreach}
<tr>
    <td colspan="4">
{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('`$form_name`', 'delete_product_section')" button_title=$lng.lbl_delete_selected acl='__1200' style="btn-danger push-5-r"}
{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('`$form_name`')" button_title=$lng.lbl_update acl='__1200' style="btn-green push-5-r"}
    </td>
</tr>

{else}
<tr class="white_bg">
    <td colspan="4" align="center">{$lng.txt_no_featured_products}</td>
</tr>
{/if}

{if $accl.__1200}
<tr class="white_bg">
    <td colspan="4">{include file="common/subheader.tpl" title=$lng.lbl_add_product}</td>
</tr>

<tr>
	<td colspan="2">
		{product_selector form="featuredproductsform_`$featured_type`" prefix_id=$featured_type}
	</td>
    <td align="center"><input type="text" name="neworder" size="5"  class="form-control" /></td>
    <td align="center">
		<input type="checkbox" name="newavail" checked="checked" />
    </td>
</tr>
{/if}
</table>

<div class="buttons col-xs-12">{include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('`$form_name`', 'add_product_section')" button_title=$lng.lbl_add_new acl='__1200' style="btn-green push-5-r push-20"}</div>



</form>
