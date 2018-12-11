{capture name=section}
{capture name=block}

<p>{$lng.txt_seller_flat_charge}</p>

<div class="box">

<form action="index.php?target={$current_target}" method="post" name="flat_charges_form">
<input type="hidden" name="action" value="update_flat_charges" />
<input type="hidden" name="seller_membership_id" value="{$membership.membership_id}"/>

<table class="table table-striped dataTable vertical-center">
<thead>
<tr>
    <th width="1%">{$lng.lbl_delete}</th>
    <th align="center">{$lng.lbl_sellers_price_range|default:'Seller\'s Price range'} ({$config.General.currency_symbol})</th>
    <th align="center">{$lng.lbl_flat_charge} ({$config.General.currency_symbol})</th>
</tr>
</thead>
{if $flat_charges}
{foreach from=$flat_charges item=flat_charge key=flat_charge_id}
<tr valign="top">
    <td align="center"><input type="checkbox" name="del[{$flat_charge_id}]" value="1"></td>
    <td align="left"><input type="text" class="micro" name="posted_data[{$flat_charge_id}][range_from]" value="{$flat_charge.range_from|default:0.00}" size="5">&nbsp;-&nbsp;<input type="text" class="micro" name="posted_data[{$flat_charge_id}][range_to]" value="{$flat_charge.range_to|default:0.00}" size="5"></td>
    <td align="left"><input type="text" class="micro" name="posted_data[{$flat_charge_id}][value]" value="{$flat_charge.value}" size="5"/></td>
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="3">{$lng.lbl_no_flat_charges_are_defined_yet|default:'No Seller\'s Flat Charges are defined yet'}</td>
</tr>
{/if}

<tr valign="top">
    <td align="center">Add new:</td>
    <td align="left"><input type="text" class="micro" name="posted_data[{$max_flat_charge_id}][range_from]" value="0.00" size="5">&nbsp;-&nbsp;<input type="text" class="micro" name="posted_data[{$max_flat_charge_id}][range_to]" value="0.00" size="5"></td>
    <td align="left"><input type="text" class="micro" name="posted_data[{$max_flat_charge_id}][value]" value="0.00" size="5"/></td>
</tr>
</table>
</form>

</div>

<div class="buttons">
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_update href="javascript:cw_submit_form(document.flat_charges_form);" js_to_href='Y' style="btn-green push-20"}
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form(document.flat_charges_form, 'delete_flat_charges');" js_to_href='Y' style="btn-danger push-20"}
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_seller_flat_charge|default:"Seller Flat Charge for '`$membership.membership`' membership"}
