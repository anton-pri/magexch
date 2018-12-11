<script type="text/javascript">
<!--
var productid = '{$productid}';

variants[productid] = [];
variant_avails[productid] = [];
{if $product.variants ne ''}
    {foreach from=$product.variants item=v key=k}
        {if $k ne 'image_thumb'}
            variant_avails[productid][{$k}] = parseInt('{$v.avail}');
            variants[productid][{$k}] = [{strip}
                [{$v.taxed_price|default:$v.price|default:$product.taxed_price|default:$product.price}, {$v.avail|default:0}, new Image(), '{$v.weight|default:0}', {$v.price|default:$product.price|default:'0'}, "{$v.productcode|escape:javascript}"],
                {ldelim}{foreach from=$v.options item=o name=opts}{if $o ne ''}{if not $smarty.foreach.opts.first}, {/if}{$o.product_option_id|default:0}: {$o.option_id|default:0}{/if}{/foreach}{rdelim},
                {ldelim}{foreach from=$v.taxes item=t key=id name=taxes}{if not $smarty.foreach.taxes.first}, {/if}{$id}: {$t|default:0}{/foreach}{rdelim},
                [{foreach from=$v.wholesale item=w key=p name=whl}		[
                        {$w.quantity|default:0},
                        {if $w.next_quantity}{math equation="x-1" x=$w.next_quantity}{else}0{/if},
                        {$w.taxed_price|default:$product.taxed_price},
                        {ldelim}{foreach from=$w.taxes item=t key=kt name=whlt}{if not $smarty.foreach.whlt.first}, {/if}{$kt}: {$t|default:0}{/foreach}{rdelim},
                        {$w.price|default:$product.price}
                    ]{if not $smarty.foreach.whl.last},{/if}
                {/foreach}]
            {/strip}];
            {if $v.is_image}
                variants[productid][{$k}][0][2].src = "{$v.image.tmbn_url}";
            {/if}
        {/if}
    {/foreach}
{/if}

modifiers[productid] = [];
// names array: as product_option_id => class name
names[productid] = [];
{foreach from=$product.options item=v key=k}
    {if $v.product_option_id}
        names[productid][{$v.product_option_id}] = {ldelim}class_name: "{$v.option_name|escape:javascript}", options: [], options_orderbys: [], show_prices: {$v.show_prices|default:0}{rdelim};
        {assign var="opt_orderby_idx" value=0}
        {foreach from=$v.options item=o name=opts}
            names[productid][{$v.product_option_id}]['options'][{$o.option_id}] = "{$o.name|escape:javascript}";
            names[productid][{$v.product_option_id}]['options_orderbys'][{$opt_orderby_idx}] = {$o.option_id};{math equation="x+1" x=$opt_orderby_idx assign="opt_orderby_idx"}
        {/foreach}
        {if $v.type eq 'Y'}
            modifiers[productid][{$v.product_option_id}] = {ldelim}{strip}
            {foreach from=$v.options item=o name=opts}
                {$o.option_id}: [
                    {$o.price_modifier|default:"0.00"},
                    '{$o.modifier_type|default:"$"}',
                    {ldelim}{foreach from=$o.taxes item=t key=id name=optt}{if not $smarty.foreach.optt.first}, {/if}{$id}: {$t|default:0}{/foreach}{rdelim}
                ]{if not $smarty.foreach.opts.last},{/if}

            {/foreach}
            {/strip}{rdelim};
        {/if}
    {/if}
{/foreach}

taxes[productid] = [];
{if $product.taxes}
    {foreach from=$product.taxes key=tax_name item=tax}
        {if $tax.display_including_tax eq "Y" && ($tax.display_info eq 'A' || $tax.display_info eq 'V')}
            taxes[productid][{$tax.tax_id}] = [{$tax.tax_value|default:0}, "{$tax.tax_display_name}", "{$tax.rate_type}", "{$tax.rate_value}"];
        {/if}
    {/foreach}
{/if}

// exceptions array: as exctionid => array: as clasid => option_id
exceptions[productid] = [];
{if $products_options_ex ne ''}
    {foreach from=$products_options_ex item=v key=k}
        exceptions[productid][{$k}] = [];
        {foreach from=$v item=o}
            exceptions[productid][{$k}][{$o.product_option_id}] = {$o.option_id};
        {/foreach}
    {/foreach}
{/if}

product_wholesale[productid] = [];
_product_wholesale[productid] = [];
{if $product_wholesale ne ''}
    {foreach from=$product_wholesale item=v key=k}
        _product_wholesale[productid][{$k}] = [{$v.quantity|default:0},{$v.next_quantity|default:0},{$v.taxed_price|default:$product.taxed_price}, [], {$v.price|default:$product.price}];
        {if $v.taxes ne ''}
            {foreach from=$v.taxes item=t key=kt}
                _product_wholesale[productid][{$k}][3][{$kt}] = {$t|default:0};
            {/foreach}
        {/if}
    {/foreach}
{/if}

current_taxes[productid] = [];
avail[productid] = [];
min_avail[productid] = {$product.min_avail|default:1};
product_thumbnail[productid] = document.getElementById('product_thumbnail_' + productid);

product_image[productid] = new Image();
product_image[productid].src = "{if $product.image_det.tmbn_url}{$product.image_det.tmbn_url}{/if}";
product_image[productid].width = "{if $product.image_det.image_x}{$product.image_det.image_x}{/if}";
product_image[productid].height = "{if $product.image_det.image_y}{$product.image_det.image_y}{/if}";
default_price[productid] = {$product.taxed_price|default:"0"};
list_price[productid] = {$product.list_price|default:0};
price[productid] = {$product.taxed_price|default:"0"};
orig_price[productid] = {$product.price|default:$product.taxed_price|default:"0"};

-->
</script>
{include_once_src file="main/include_js.tpl" src="addons/product_options/js/func-list.js"}
