{if $doc.products}
<table class="header" width="100%">
<tr>
    <th width="10%">{$lng.lbl_sku}</th>
    <th>{$lng.lbl_product}</th>
    <th width="10%">{$lng.lbl_amount}</th>
    <th>{$lng.lbl_serials_numbers}</th>
</tr>
{foreach from=$doc.products item=product}
<tr>
    <td align="center">{$product.productcode}</td>
    <td><a href="index.php?target=products&mode=details&product_id={$product.product_id}&js=serial_numbers" target=_blank>{$product.product}</a>
{* kornev, TOFIX *}
    {if $product.product_options ne ''}
    <div><b>{$lng.lbl_options}:</b></div>
    {include file="addons/product_options/main/options/display.tpl" options=$product.product_options options_txt=$product.product_options_txt force_product_options_txt=$product.force_product_options_txt}
    {/if}
    </td>
    <td align="center">{$product.amount}</td>
    <td>
    <form action="index.php?target={$current_target}&mode=details&doc_id={$doc.doc_id}" method="post" name="serial_numbers_{$product.item_id}">
    <input type="hidden" name="action" value="serial_numbers" />
    <input type="hidden" name="item_id" value="{$product.item_id}" />

<table class="header" width="100%">

{if $product.serial_numbers}
{array_chunk var=$product.serial_numbers assign="product_serial_numbers" cols=$config.sn.serial_per_row_order}
{foreach from=$product_serial_numbers item=chunk}
<tr>
    {foreach from=$chunk item=serial}
    <td><input type="checkbox" name="serial[{$serial.id}][del]" value="1" /></td>
    <td>{$serial.number}</td>
    {/foreach}
</tr>
{/foreach}
{/if}
</table>

{include file='addons/sn/enter_serials.tpl' id_prefix='serial_numbers'}
{include file='buttons/button.tpl' button_title=$lng.lbl_add_serial_numbers href="javascript: if (cw_check_serials_form('serial_numbers', 'serial_numbers')) cw_submit_form('serial_numbers_`$product.item_id`');"}

    </td>
</tr>
{/foreach}
</table>
{/if}
