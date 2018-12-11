<div id='top_minicart' blockUI='top_minicart'>
{if $cart.products}
<form id="minicartform" name="minicartform" method="post" action="index.php?target=cart&get_top_minicart=1">
<input type="hidden" value="update" name="action" />
{*$cart.products|@debug_print_var*}
<div class="minicart_products">
<div class='minicart_content'>
{foreach from=$cart.products item=minicart_prod name=minicart}

<div class='minicart_item {cycle values="cycle,"} {if $smarty.foreach.minicart.first}first{/if}'>
    <!-- cw@minicart_image [ -->
    <div class='minicart_image' width='48%' align="left">
        <a href='{pages_url var="product" product_id=$minicart_prod.product_id}'>{include file='common/product_image.tpl' product_id=$minicart_prod.product_id image=$minicart_prod.image_thumb id='product_thumbnail'}</a>
    </div>
    <!-- cw@minicart_image ] -->

    <!-- cw@minicart_prod [ -->
    <div class='minicart_prod' width='48%'>
        <span class="quantity-formated"><span class="qty">{$minicart_prod.amount}</span> x </span><a class="top_minicart_link" href='{pages_url var="product" product_id=$minicart_prod.product_id}'>{$minicart_prod.product}</a>
        <div class='minicart_price'>{include file='common/currency.tpl' value=$minicart_prod.display_price|default:0}
            {if $config.Taxes.display_taxed_order_totals eq "Y" and $minicart_prod.taxes}
                <span>{include file="customer/main/taxed_price.tpl" taxes=$minicart_prod.taxes}</span>
            {/if}
        </div>
    </div>
    <!-- cw@minicart_prod ] -->

    <!-- cw@minicart_delete [ -->
    <div class='minicart_delete' width='4%'>
        <a class='top_minicart_control' href='javascript: void(0);' onclick="ajaxGet('index.php?target=cart&action=delete&productindex={$minicart_prod.cartid}&get_top_minicart=1', 'top_minicart');"></a>
    </div>
    <!-- cw@minicart_delete ] -->

</div>
{/foreach}
</div>
</div>
<!-- cw@minicart_summary [ -->
<table class='minicart_total' cellpadding='2' cellspacing='0'>
<tr class='minicart_summary'>
    <td>{$lng.lbl_subtotal}:</td>
    <td class='minicart_price'>
        {if $cart}{include file='common/currency.tpl' value=$cart.info.display_subtotal|default:0}{else}{include file='common/currency.tpl' value=0}{/if}
    </td>
</tr>
</table>
<!-- cw@minicart_summary ] -->

<!-- cw@minicart_menu [ -->
<div class='minicart_menu'>
{*    <a class="top_minicart_link float-left" href='javascript: void(0);' onclick="blockElements('top_minicart',true); submitFormAjax('minicartform',$.unblockUI);">Update</a>*}
    <a class="top_minicart_link float-left" href='index.php?target=cart'>View cart</a>
    <a class='minicart_control top_minicart_link float-right' href='javascript: void(0);' onclick="ajaxGet('index.php?target=cart&action=clear_cart', 'top_minicart', microcart_content_hide);">Clear cart</a>
    <div class='clear'></div>
    <a class="top_minicart_link checkout" href='index.php?target=cart&mode=checkout'>Checkout <i class="icon-chevron-right"></i></a>

</div>
<!-- cw@minicart_menu ] -->

</form>
{else}
<div class="empty_cart">
{$lng.lbl_cart_is_empty}
</div>
{/if}
</div>

<script type="text/javascript">
{assign_session var='cart' assign='cart'}
var _count_products_in_cart = '({$cart.products|@count})';
var _lbl_cart_is_empty = '{$lng.lbl_your_cart_is_empty}';
var _lbl_cart_items = '{$lng.lbl_cart_items}';

{if $config.ajax_add2cart.place_where_display_minicart eq 1}
    {literal}
        $(document).ready(function() {
            if (
                _count_products_in_cart == 0
                && _lbl_cart_is_empty != $.trim($('#microcart a').html())
            ) {
                $('#microcart a span.cart_qty').html(0);
                microcart_content_hide();
            }
            else {
                $('#microcart a span.cart_qty').html(_count_products_in_cart);
            }
        });
    {/literal}
{/if}
</script>
