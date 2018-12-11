
{if $ge_id}<b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}<br/>{/if}

<table width="100%" class="table table-striped dataTable vertical-center" border="0">
<thead>
<tr>
	{if $ge_id}<th width="15">&nbsp;</th>{/if}
	<th class="text-center">{$lng.lbl_del}</th>
{if $addons.wholesale_trading}
	<th class="text-center">{$lng.lbl_quantity}</th>
{/if}
{if $product.is_variants eq 'Y'}
    <th class="text-center">{$lng.lbl_variant}</th>
{/if}
    <th class="text-center">{$lng.lbl_price}</th>
    <th class="text-center">{$lng.lbl_list_price}</th>
    <th class="text-center">{$lng.lbl_membership}</th>
</tr>
</thead>
{if $products_prices}
{foreach from=$products_prices key=index item=v}
{if $v.membership_id eq 0 and $v.quantity eq 1}{assign var="locked_line" value="1"}{else}{assign var="locked_line" value="0"}{/if}
<tr{cycle values=' class="cycle",'}>
	{if $ge_id}<td><input type="checkbox" value="1" name="fields[w_price][{$v.price_id}]" /></td>{/if}
	<td align="center"><input type="checkbox" name="{$prefix}[{$v.price_id}][del]" value="1" {if $locked_line}DISABLED{/if} /></td>
{if $addons.wholesale_trading}
	<td align="center">
        <input type="hidden" name="{$prefix}[{$v.price_id}][quantity_old]" value="{$v.quantity}" />
        <input class="form-control" type="text" name="{$prefix}[{$v.price_id}][quantity]" value="{$v.quantity}" size="5" {if $locked_line}DISABLED{/if} />
    </td>
{else}
    <input type="hidden" name="{$prefix}[{$v.price_id}][quantity_old]" value="{$v.quantity}" />
{/if}
{if $product.is_variants eq 'Y'}
    <td align="center">{include file='main/select/product_variant.tpl' name="`$prefix`[`$v.price_id`][variant_id]" value=$v.variant_id class='form-control'}</td>
{/if}
    <td align="center"><input class="form-control" type="text" name="{$prefix}[{$v.price_id}][price]" value="{$v.price|formatprice}" size="6" /></td>
    <td align="center"><input class="form-control" type="text" name="{$prefix}[{$v.price_id}][list_price]" value="{$v.list_price|formatprice}" size="6"/></td>
    {if $locked_line}{assign var="disabled" value="1"}{/if}
    <td align="center">{include file='admin/select/membership.tpl' name="`$prefix`[`$v.price_id`][membership_id]"  value=$v.membership_id}
        <input type="hidden" name="{$prefix}[{$v.price_id}][membership_id_old]" value="{$v.membership_id}" /></td>
    {assign var="disabled" value="0"}
</tr>
{/foreach}
{else}
<tr>
    <td colspan="10" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
{if $accl.$page_acl || !$page_acl}
<tr>
	<td colspan="10">{include file='common/subheader.tpl' title=$lng.lbl_add_new_price}</td>
</tr>
<tr>
	{if $ge_id}<td><input type="checkbox" value="1" name="fields[w_price][0]" /></td>{/if}
	<td>&nbsp;</td>
{if $addons.wholesale_trading}
	<td align="center"><input class="form-control" type="text" name="{$prefix}[0][quantity]" size="8" /></td>
{/if}
{if $product.is_variants eq 'Y'}
    <td>{include file='main/select/product_variant.tpl' name="`$prefix`[0][variant_id]" value=0 class='form-control'}</td>
{/if}
    <td align="center"><input class="form-control" type="text" name="{$prefix}[0][price]" value="{$zero}" size="6" /></td>
    <td align="center"><input class="form-control" type="text" name="{$prefix}[0][list_price]" value="{$zero}" size="6" /></td>
    <td align="center">{include file='admin/select/membership.tpl' name="`$prefix`[0][membership_id]" value=''}</td>
</tr>
{/if}
</table>
