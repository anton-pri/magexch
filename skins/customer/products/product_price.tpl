        <div class="product_field taxed_price{cycle values=", cycle"}">
            {if $product.taxed_price gt 0 and $product.list_price gt 0}
            <!-- cw@sale_price [ -->
            {math equation="100-(price/lprice)*100" price=$product.display_price lprice=$product.list_price format="%3.0f" assign=discount}
            {math equation="lprice-price" price=$product.display_price lprice=$product.list_price assign=discount_value}
              <span class="list_price">{include file='common/currency.tpl' value=$product.list_price plain_text_message=true}</span>
            <!-- cw@sale_price ] -->

            {/if}
            <!-- cw@our_price [ -->

            <span class="our_price"><span id="product_price">
            {if $product.taxed_price ne 0 || $variant_price_no_empty}
                {include file='common/currency.tpl' value=$product.display_price plain_text_message=true}
                {include file='common/alter_currency_value.tpl' alter_currency_value=$product.display_price plain_text_message=true}
            {else}
            <input type="text" size="7" name="price" />
            {/if}
            </span>
            <!-- cw@our_price ] -->

            <!-- cw@taxes [ -->
            {if $product.taxes}<span class="incl_tax">{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}</span>{/if}
            <!-- cw@taxes ] -->


            <!-- cw@free_shipping [ -->
            {include file='customer/products/free_shipping.tpl'}
            <!-- cw@free_shipping ] -->

            </span>
        </div>

        <!-- cw@you_save [ -->
        {if $product.taxed_price gt 0 and $product.list_price gt 0}
        <div class="product_field{cycle values=", cycle"}  height">
          <span class="you_save">
            {$lng.lbl_you_save}
            <span id='save_percent_box'>{include file='common/currency.tpl' value=$discount_value plain_text_message=true} ({$discount|strip:''}%)</span>
          </span>
        </div>
        {/if}
        <!-- cw@you_save ] -->


{* kornev, there is the checkoptions javascript...*}

{if $product_wholesale}
<div class="wholesale">
<p>{$lng.lbl_wholesale_prices_title}</p>

<table  class="header bordered">
{section name=wi loop=$product_wholesale}
<tr{cycle values=" class='cycle',"}>
	<td width="33%">
        {$lng.lbl_buy} <font class="WholesalePrice">{$product_wholesale[wi].quantity}</font> {$lng.lbl_or_more}
	</td>
    <td width="33%">{$lng.lbl_pay_only} <font class="WholesalePrice">{if !$no_span}<span id="wp{%wi.index%}">{/if}{include file='common/currency.tpl' value=$product_wholesale[wi].taxed_price}{if !$no_span}</span>{/if}</font> {$lng.lbl_per_item}</td>
    <td width="33%">
        {$lng.lbl_you_save} <font class="WholesalePrice">
        {math equation="(price/lprice)*100-100" price=$product.taxed_price lprice=$product_wholesale[wi].taxed_price format="%3.2f" assign=discount}
        {math equation="price-lprice" price=$product.taxed_price lprice=$product_wholesale[wi].taxed_price format="%0.2f" assign=discount_flat}
        {if !$no_span}<span id="ws{%wi.index%}">{/if}{include file='common/currency.tpl' value=$discount_flat} {$lng.lbl_or} {$discount}%{if !$no_span}</span>{/if}</font>
    </td>
</tr>
{/section}
</table>
</div>
{/if}
