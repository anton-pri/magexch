<div class="dialog_title">{$lng.txt_affiliate_plan_management_note}</div>

<form action="index.php?target={$current_target}" name="commisions_form" method="post">
<input type="hidden" name="action" value="modify" />
<input type="hidden" name="plan_id" value="{$plan_id}" />

<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_del}</th>
	<th width="70%">{$lng.lbl_item}</th>
	<th width="20%">{$lng.lbl_commission_rate}</th>
    <th width="10%">{$lng.lbl_membership}</th>
</tr>
{if $salesman_plans_commissions}
{foreach from=$salesman_plans_commissions item=comm}
<tr{cycle values=', class="cycle"'}>
	<td><input type="checkbox" name="product_id[]" value="{$comm.id}" /></td>
	<td>
    {if $comm.item_type eq 'P'}
        <a href="index.php?target=products&mode=edit&product_id={$comm.item_id}">{$comm.product.product}</a>
    {elseif $comm.item_type eq 'C'}
        <a href="index.php?target=category_modify&cat={$comm.item_id}">{$comm.category.category}</a>
    {/if}
    </td>
	<td>
        <input type="text" name="products[{$comm.id}][commission]" value="{$comm.commission}" size="10"/>
        {include file='main/select/pf_type.tpl' name="products[`$comm.id`][commission_type]" value=$comm.commission_type}
	</td>
    <td>
        {include file='main/select/membership.tpl' name="products[`$comm.id`][membership_id]" value=$comm.membership_id}
    </td>
</tr>
{/foreach}
{else}
<tr>
	<td colspan="6" align="center">{$lng.txt_no_products_commission}</td>
</tr>
{/if}
{if $accl.__1101}
<tr>
    <td>&nbsp;</td>
	<td>
		{product_selector name_for_id="products[0][product_id]" name_for_name="products0product_id_name"}<br/>
        {include file="main/select/category.tpl" name="products[0][category_id]"}
	</td>
	<td>    
        <input type="text" name="products[0][commission]" value="" size="10" />
        {include file='main/select/pf_type.tpl' name="products[0][commission_type]"}
	</td>
    <td>
        {include file='main/select/membership.tpl' name='products[0][membership_id]'}
    </td>
</tr>
{/if}
</table>
{include file='buttons/button.tpl' button_title=$lng.lbl_add_update href="javascript: cw_submit_form('commisions_form')" acl='__1101'}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript: cw_submit_form('commisions_form', 'delete_rate')" acl='__1101'}

{include file="common/subheader.tpl" title=$lng.lbl_aff_plans_general_settings}
<table class="header">
{foreach from=$memberships item=mem}
{assign var="memid" value=$mem.membership_id}
{assign var="basic" value=$general_commission.$memid}
<tr>
	<th>{$mem.membership}</th>
	<td><input type="text" name="basic[{$memid}][commission]" value="{$basic.commission|formatprice|default:$zero}" size="10" /></td>
	<td>
        {include file='main/select/pf_type.tpl' name="basic[`$memid`][commission_type]" value=$basic.commission_type}
	</td>
</tr>
{/foreach}

<tr>
	<th>{$lng.lbl_minimum_commission_payment}</th>
	<td><input type="text" name="min_paid" size="10" value="{$salesman_plan_info.min_paid|formatprice|default:$zero}" /></td>
</tr>
</table>

{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('commisions_form')" acl='__1101'}

</form>
