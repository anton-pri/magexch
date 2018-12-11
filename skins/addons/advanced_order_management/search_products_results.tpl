{include file='common/subheader.tpl' title=$lng.lbl_search_results}
<table class="header" width="100%">
<tr>
    <th width="1%">{$lng.lbl_add}</th>
{if $is_old_products}
    <th width="1%">{$lng.lbl_add_to_old}</th>
{/if}
    <th width="10%">{$lng.lbl_eancode}</th>
    <th>{$lng.lbl_product}</th>
    <th width="10%">{$lng.lbl_net_price}</th>
    <th width="10%">{$lng.lbl_supplier}</th>
    <th width="10%">{$lng.lbl_manufacturer}</th>
    <th width="10%">{$lng.lbl_quantity}</th>
</tr>
{if $products}
{foreach from=$products item=product key=index}
<tr{cycle name=$type values=", class='cycle'"}>
    <td><input type="checkbox" onclick="javascript: cw_doc_add_item_info_by_product_id('{$doc_id}', '{$product.product_id}', 'product_search_{$product.product_id}');" onmouseout="javascript: if (this.checked) {ldelim} sleep(1000); this.checked=false;{rdelim}"></td>
{if $is_old_products}
    <td><input type="checkbox" onclick="javascript: cw_doc_add_item_info_by_product_id('{$doc_id}', '{$product.product_id}', 'product_search_{$product.product_id}', 1);" onmouseout="javascript: if (this.checked) {ldelim} sleep(1000); this.checked=false;{rdelim}"></td>
{/if}
    <td>{$product.eancode}</td>
    <td>{$product.product}</td>
    <td align="center">{include file='common/currency.tpl' value=$product.display_price}</td>
    <td>
        {if $product.suppliers}
        {foreach from=$product.suppliers item=supplier}
        {$supplier}<br/>
        {/foreach}
        {/if}
    </td>
    <td>{$product.manufacturer}</td>
    <td><input type="text" id="product_search_{$product.product_id}" value="1" size="6"></td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="7" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>
