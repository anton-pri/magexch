{assign var='prefix' value='datahub_price_levels'}

<table width="100%" class="table table-striped dataTable vertical-center" border="0">
<thead>
<tr>
    <th class="text-center">{$lng.lbl_del}</th>
    <th class="text-center">{$lng.lbl_cost}</th>
    <th class="text-center">{$lng.lbl_surcharge|default:'Surcharge, %'}</th>
</tr>
</thead>
{if $price_levels}
{foreach from=$price_levels key=index item=v}
<tr{cycle values=' class="cycle",'}>
    <td align="center"><input type="checkbox" name="{$prefix}[{$v.level_id}][del]" value="1" /></td>
    <td align="center"><input class="form-control" type="text" name="{$prefix}[{$v.level_id}][cost]" value="{$v.cost|formatprice}" size="6" /></td>
    <td align="center"><input class="form-control" type="text" name="{$prefix}[{$v.level_id}][surcharge]" value="{$v.surcharge|formatprice}" size="6"/></td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="10" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
<tr>
	<td colspan="10">{include file='common/subheader.tpl' title=$lng.lbl_add_new_cost_level|default:'Add New Level'}</td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td align="center"><input class="form-control" type="text" name="{$prefix}[0][cost]" value="{$zero}" size="6" /></td>
    <td align="center"><input class="form-control" type="text" name="{$prefix}[0][surcharge]" value="{$zero}" size="6" /></td>
</tr>
</table>
