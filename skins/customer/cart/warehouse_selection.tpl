<select name="warehouse_selection[{$product.cartid}]">
{foreach from=$insider_warehouses item=warehouse}
{assign var="key" value=$warehouse.customer_id}
<option value="{$key}"{if $product.destination_warehouse eq $key} selected="selected"{/if}>{$warehouse.warehouse_title}</option>
{/foreach}
</select>
