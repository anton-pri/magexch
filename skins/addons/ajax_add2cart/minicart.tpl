{if $config.ajax_add2cart.place_where_display_minicart eq 0}
    <div id='minicart' blockUI='minicart'>
    {if $cart.products}
        <form id="minicartform" name="minicartform" method="post" action="{$current_location}/index.php?target=cart">
        <input type="hidden" value="update" name="action" />

        <table class='minicart_content' cellpadding='2' cellspacing='0'>
        {foreach from=$cart.products item=minicart_prod}
            <tr class='minicart_item {cycle values="cycle,"}' valign='top'>
                <td class='minicart_prod'>
                    <a href='{pages_url var="product" product_id=$minicart_prod.product_id}'>{$minicart_prod.product}</a>
                    <div class='minicart_price'>{include file='common/currency.tpl' value=$minicart_prod.display_price|default:0}
                    {if $config.Taxes.display_taxed_order_totals eq "Y" and $minicart_prod.taxes}
                        <span>{include file="customer/main/taxed_price.tpl" taxes=$minicart_prod.taxes}</span>
                    {/if}
                    </div>
                </td>
                <td class='minicart_delete' width='10'>
                    <a class='minicart_control' href='{$current_location}/index.php?target=cart&action=delete&productindex={$minicart_prod.cartid}'></a>
                </td>
                <td class='minicart_qty'>
                    <div><input id="productindexes_{$minicart_prod.cartid}" type="text" value="{$minicart_prod.amount}" name="productindexes[{$minicart_prod.cartid}]" size="2"></div>
                </td>
            </tr>
        {/foreach}
            <tr class='minicart_summary'>
                <td colspan="2">{$lng.lbl_subtotal}:</td>
                <td class='minicart_price'>{if $cart}{include file='common/currency.tpl' value=$cart.info.display_subtotal|default:0}{else}{include file='common/currency.tpl' value=0}{/if}</td>
            </tr>
        </table>

        <div class='minicart_menu'>
            <div class="float-left"><a href='javascript: void(0);' onclick="blockElements('minicart',true); submitFormAjax('minicartform',$.unblockUI);">Update</a></div>
            <div class="float-right"><a class='minicart_control' href='{$current_location}/index.php?target=cart&action=clear_cart'>Clear cart</a></div>
        </div>
        </form>
    {else}
        <div class="empty_cart">
        {$lng.lbl_cart_is_empty}
        </div>
    {/if}
    </div>
{/if}
