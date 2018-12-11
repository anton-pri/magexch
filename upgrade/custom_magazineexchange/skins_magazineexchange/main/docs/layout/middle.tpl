<div class="row items-push-2x">
<div class="col-xs-12">
{if $product_layout_elements eq ''}{tunnel func='cw_web_get_product_layout_elements' via='cw_call' assign='product_layout_elements'}{/if}
{if $main eq 'layout'}
<div id="doc_products_container" class="table-responsive push-50">
<table class="table table-bordered table-hover" id="doc_products" >
<thead>
{else}
<div id="doc_products">
<table class="table table-bordered table-hover" >
{/if}
<tr>
    {foreach from=$layout_data.data.elements item=el}
    {assign var='wdh_name' value="table_cell_`$el`"}
    {assign var='wdh' value=$elements.$wdh_name}
    <th width="{$wdh.width}">{lng name=$product_layout_elements.$el}</th>
    {/foreach}
</tr>
{if $main eq 'layout'}
</thead>
<tbody>
{/if}
{if $products}
{foreach from=$products item=product}

<tr>
    {foreach from=$layout_data.data.elements item=el}
    <td{if $el eq 'product_id' || $el eq 'sku' || $el eq 'supplier_sku' || $el eq 'amount' || $el eq 'display_net_price' || $el eq 'discount' || $el eq 'display_subtotal'} align="middle"{/if} class="doc_products_layout_element_{$el}" style="border: 1px solid #ddd;">
{if $el eq 'tax'}
        {foreach from=$product.extra_data.taxes key=tax_name item=tax}
        {if $tax.tax_value gt 0}
            {if $order.extra.tax_info.product_tax_name eq ""}{$tax.tax_display_name} {/if}
            {if $tax.rate_type eq "%"}{$tax.rate_value}%{else}{include file='common/currency.tpl' value=$tax.rate_value}{/if}<br />
        {/if}
        {/foreach}
{elseif $el eq 'product'}
   {* #{$product.product_id}&nbsp; *} {$product.product}<br />
    {if $product.destination_warehouse}
        <div><b>{$lng.lbl_delive_to_warehouse}: {$product.destination_warehouse_title}</b></div>
    {/if}

    {include file='addons/custom_magazineexchange_sellers/main/download_link.tpl' product=$product userinfo=$userinfo status=$doc.status}

{* kornev, TOFIX *}
    {if $product.product_options ne ''}
        <div><b>{$lng.lbl_options}:</b></div>
        {include file="addons/product_options/main/options/display.tpl" options=$product.product_options options_txt=$product.product_options_txt force_product_options_txt=$product.force_product_options_txt}
    {/if}

    <!-- cw@ordered_product_extra -->

    {if $product.serial_numbers}
        <div><b>{$lng.lbl_serial_numbers}:</b></div>
    {foreach from=$product.serial_numbers item=sn}
        {$sn.number}<br/>
    {/foreach}
    {/if}

    {if $addons.egoods and $product.download_key and ($order.status eq "P" or $order.status eq "C")}
        <br />
        <a href="index.php?target=download&id={$product.download_key}" class="SmallNote" target="_blank">{$lng.lbl_download}</a>
    {/if}

{elseif $el eq 'discount'}
    {math assign="total" equation="net_price-price/amount" amount=$product.amount price=$product.display_subtotal net_price=$product.display_net_price}
    {if $total > 0}
        {include file='common/currency.tpl' value=$total}
    {else}
        {include file='common/currency.tpl' value=0}
    {/if}
{elseif $el eq 'display_net_price' || $el eq 'display_subtotal'}
    {if $el eq 'display_net_price'}
        {include file='common/currency.tpl' value=$product.display_price}
    {else}
        {include file='common/currency.tpl' value=$product.$el}
    {/if}
{else}
{$product.$el|default:"&nbsp;"}
{/if}
    </td>
    {/foreach}
</tr>
{/foreach}
{elseif $main eq 'layout'}
<tr>
    {foreach from=$layout_data.data.elements item=el}
    {assign var='wdh_name' value="table_cell_`$el`"}
    {assign var='wdh' value=$elements.$wdh_name}
    <td>{lng name=$product_layout_elements.$el}</td>
    {/foreach}
</tr>
{/if}

{if $giftcerts ne ''}
{foreach from=$giftcerts item=gc}
<tr>
    {foreach from=$layout_data.data.elements item=el}
        <td nowrap="nowrap"{if $el eq 'amount' || $el eq 'display_net_price' || $el eq 'discount' || $el eq 'display_subtotal'} align="center"{/if}>        
            {if $el eq 'product_id' || $el eq 'product'}
                {$lng.lbl_gift_certificate}: {$gc.gc_id}<br />
                <div style="padding-left: 10px; white-space: nowrap;">
                    {if $gc.send_via eq "P"}
                        {$lng.lbl_gc_send_via_postal_mail}<br />
                        {$lng.lbl_mail_address}: {$gc.recipient_firstname} {$gc.recipient_lastname}<br />
                        {$gc.recipient_address}, {$gc.recipient_city},<br />
                        {if $gc.recipient_countyname ne ''}{$gc.recipient_countyname} {/if}{$gc.recipient_state} {$gc.recipient_country}, {$gc.recipient_zipcode}<br />
                        {$lng.lbl_phone}: {$gc.recipient_phone}
                    {else}
                        {$lng.lbl_recipient_email}: {$gc.recipient_email}
                    {/if}
                </div>
            {elseif $el eq 'discount'}
                {include file='common/currency.tpl' value=0}
            {elseif $el eq 'display_net_price' || $el eq 'display_subtotal'}
                {include file='common/currency.tpl' value=$gc.amount}
            {elseif $el eq 'amount'}
                1
            {else}
                {$gc.$el|default:"&nbsp;"}
            {/if}
        </td>
    {/foreach}
</tr>
{/foreach}
{/if}
{if $main eq 'layout'}
</tbody>
{/if}
</table>
</div>
</div>
</div>
