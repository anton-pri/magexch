{if $shipping}
<select name="total_details[shipping_id]" onchange="javascript: aom_change_shipping(this.value);">
<option value="0">{$lng.lbl_aom_shipmethod_notavail}</option>
{foreach from=$shipping item=ship}
<option value="{$ship.shipping_id}"{if $ship.shipping_id eq $order.info.shipping_id} selected="selected"{/if}>{$ship.shipping|trademark:$insert_trademark:"alt"} ({include file='common/currency.tpl' value=$ship.rate plain_text_message=1})</option>
{/foreach}
</select>
{else}
{$lng.lbl_aom_shipmethod_notavail}
{/if}
