{if !$product.avail}

<script type="text/javascript">
<!--
var min_avail = 1;
var avail = 1;
var product_avail = 1;
-->
</script>
{* kornev, TOFIX *}
<select id="product_avail" name="amount"{if $product_options} onchange="rebuild_wholesale();"{/if}>
<option value="0">{$lng.lbl_out_of_stock}</option>
</select>

{else}
    {if $product_avail.settings.unlimited_products}
        {assign var="mq" value=$config.Appearance.max_select_quantity}
    {else}

        {if $product.min_amount gt 1}
          {math equation="x/y" x=$config.Appearance.max_select_quantity y=$product.min_amount assign="tmp"}
        {else}
          {assign var='tmp' value=$config.Appearance.max_select_quantity}
        {/if}
        {if $tmp<2}
            {assign var="minamount" value=$product.min_amount}
        {else}
            {assign var="minamount" value=1}
        {/if}
        {math equation="min(maxquantity+minamount, productquantity+1)" assign="mq" maxquantity=$config.Appearance.max_select_quantity minamount=$minamount productquantity=$product.avail}
        {if $mq lt 0}{assign var='mq' value=1}{/if}
    {/if}
    {if !$product.distribution}
        {if $product.min_amount le 1}
            {assign var="start_quantity" value=1}
        {else}
            {assign var="start_quantity" value=$product.min_amount}
        {/if}
        {if $product_avail.settings.unlimited_products}
            {math equation="x+y" assign="mq" x=$mq y=$start_quantity}
        {/if}
<script type="text/javascript">
<!--
var min_avail = {$start_quantity|default:1};
var avail = {$mq|default:1}-1;
var product_avail = {$product.avail|default:0};
-->
</script>
{* kornev, TOFIX, add action by jquery *}
<select id="product_avail" name="amount"{if $product_options} onchange="rebuild_wholesale();"{/if}>
{section name=quantity loop=$mq start=$start_quantity}
<option value="{$smarty.section.quantity.index}" {if $smarty.get.quantity eq $smarty.section.quantity.index}selected{/if}>{$smarty.section.quantity.index}</option>
{/section}
</select>
    {else}
<script type="text/javascript">
<!--
var min_avail = 1;
var avail = 1;
var product_avail = 1;
-->
</script>
<div class="prod_amount">
<font class="ProductDetailsTitle">1</font><input type="hidden" name="amount" value="1" /> {if $product.distribution}{$lng.txt_product_downloadable}{/if}
</div>
    {/if}
{/if}
