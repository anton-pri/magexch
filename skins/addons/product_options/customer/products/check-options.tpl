<script type="text/javascript">
{literal}
/*
variants[variant_id] = [
    [taxed_price, avail, image, weight, price, productcode],
    {product_option_id:option_id, ...},
    {tax_id:tax, ...},
    [   // wholesale prices
        [quantity, next_quantity, taxed_price, {tax_id:tax}, price, list_price],
        ...
    ]
]
*/
{/literal}
var variants = [];
var variant_avails = [];
{if $variants ne ''}
{foreach from=$variants item=v key=k}
variant_avails[{$k}] = parseInt('{$v.avail}');
variants[{$k}] = [{strip}
	[{$v.taxed_price|default:$v.price|default:$product.taxed_price|default:$product.price}, {$v.avail|default:0}, new Image(), '{$v.weight|default:0}', {$v.price|default:$product.price|default:'0'}, "{$v.productcode|escape:javascript}"],
	{ldelim}{foreach from=$v.options item=o name=opts}{if $o ne ''}{if not $smarty.foreach.opts.first}, {/if}{$o.product_option_id|default:0}: {$o.option_id|default:0}{/if}{/foreach}{rdelim},
	{ldelim}{foreach from=$v.taxes item=t key=id name=taxes}{if not $smarty.foreach.taxes.first}, {/if}{$id}: {$t|default:0}{/foreach}{rdelim},
	[{foreach from=$v.wholesale item=w key=p name=whl}

		[
			{$w.quantity|default:0},
			{if $w.next_quantity}{math equation="x-1" x=$w.next_quantity}{else}0{/if},
			{$w.taxed_price|default:$product.taxed_price},
			{ldelim}{foreach from=$w.taxes item=t key=kt name=whlt}{if not $smarty.foreach.whlt.first}, {/if}{$kt}: {$t|default:0}{/foreach}{rdelim},
			{$w.price|default:$product.price},
            {$w.list_price|default:$product.list_price|default:0}
		]{if not $smarty.foreach.whl.last},{/if}
{/foreach}]
{/strip}];
{if $v.is_image}
variants[{$k}][0][2].src = "{$v.image.tmbn_url}";
{/if}
{/foreach}
{/if}

var modifiers = [];
// names array: as product_option_id => class name
var names = [];
{foreach from=$product_options item=v key=k}
names[{$v.product_option_id}] = {ldelim}class_name: "{$v.option_name|escape:javascript}", options: [], options_orderbys: [], show_prices: {$v.show_prices|default:0}{rdelim};
{assign var="opt_orderby_idx" value=0}
{foreach from=$v.options item=o name=opts}
names[{$v.product_option_id}]['options'][{$o.option_id}] = "{$o.name|escape:javascript}";
names[{$v.product_option_id}]['options_orderbys'][{$opt_orderby_idx}] = {$o.option_id}; {math equation="x+1" x=$opt_orderby_idx assign="opt_orderby_idx"}
{/foreach}
{if $v.type eq 'Y'}
modifiers[{$v.product_option_id}] = {ldelim}{strip}
{foreach from=$v.options item=o name=opts}
	{$o.option_id}: [
		{$o.price_modifier|default:"0.00"}, 
		'{$o.modifier_type|default:"$"}',
		{ldelim}{foreach from=$o.taxes item=t key=id name=optt}{if not $smarty.foreach.optt.first}, {/if}{$id}: {$t|default:0}{/foreach}{rdelim}
	]{if not $smarty.foreach.opts.last},{/if}

{/foreach}
{/strip}{rdelim};
{/if}
{/foreach}

var taxes = [];
{if $product.taxes}
{foreach from=$product.taxes key=tax_name item=tax}
{if $tax.display_including_tax eq '1' && ($tax.display_info eq 'A' || $tax.display_info eq 'V')}
taxes[{$tax.tax_id}] = [{$tax.tax_value|default:0}, "{$tax.tax_display_name}", "{$tax.rate_type}", "{$tax.rate_value}"];
{/if}
{/foreach}
{/if}

// exceptions array: as exctionid => array: as clasid => option_id
var exceptions = [];
{if $products_options_ex ne ''}
{foreach from=$products_options_ex item=v key=k}
exceptions[{$k}] = [];
{foreach from=$v item=o}
exceptions[{$k}][{$o.product_option_id}] = {$o.option_id};
{/foreach} 
{/foreach} 
{/if}

var product_wholesale = [];
var _product_wholesale = [];
{if $product_wholesale ne ''}
{foreach from=$product_wholesale item=v key=k}
_product_wholesale[{$k}] = [{$v.quantity|default:0},{$v.next_quantity|default:0},{$v.taxed_price|default:$product.taxed_price}, [], {$v.price|default:$product.price}];
{if $v.taxes ne ''}
{foreach from=$v.taxes item=t key=kt}
_product_wholesale[{$k}][3][{$kt}] = {$t|default:0};
{/foreach}
{/if}
{/foreach}
{/if}

var product_image = new Image();
product_image.src = "{if $product.image_det.tmbn_url}{product_image product_id=$product.product_id image=$product.image_det just_url='Y'}{/if}";
var exception_msg = "{$lng.txt_exception_warning|strip_tags|escape:javascript}!";
var exception_msg_html = "{$lng.txt_exception_warning|escape:javascript}!";
var txt_out_of_stock = "{$lng.txt_out_of_stock|strip_tags|escape:javascript}";
var default_price = {$product.taxed_price|default:"0"};
var currency_symbol = "{$config.General.currency_symbol|escape:"javascript"}";
var alter_currency_symbol = "{$config.General.alter_currency_symbol|escape:"javascript"}";
var alter_currency_rate = {$config.General.alter_currency_rate|default:"0"};
var lbl_no_items_available = "{$lng.lbl_no_items_available|escape:javascript}";
var txt_items_available = "{$lng.txt_items_available|escape:javascript}";
var list_price = {$product.list_price|default:0};
var price = {$product.taxed_price|default:"0"};
var orig_price = {$product.price|default:$product.taxed_price|default:"0"};
var mq = {$config.Appearance.max_select_quantity|default:0};
var dynamic_save_money_enabled = {if $config.Product_Options.dynamic_save_money_enabled eq 'Y'}true{else}false{/if};
var is_unlimit = false;

var lbl_item = "{$lng.lbl_item|escape:javascript}";
var lbl_items = "{$lng.lbl_items|escape:javascript}";
var lbl_quantity = "{$lng.lbl_quantity|escape:javascript}";
var lbl_price = "{$lng.lbl_price_per_item|escape:javascript}";
var txt_note = "{$lng.txt_note|escape:javascript}";
var lbl_including_tax = "{$lng.lbl_including_tax|escape:javascript}";
var lbl_buy= "{$lng.lbl_buy|escape:javascript}";
var lbl_or_more= "{$lng.lbl_or_more|escape:javascript}";
var lbl_pay_only="{$lng.lbl_pay_only|escape:javascript}";
var lbl_per_item="{$lng.lbl_per_item|escape:javascript}";
var lbl_you_save="{$lng.lbl_you_save|escape:javascript}";
var lbl_or="{$lng.lbl_or|escape:javascript}";
</script>
{include_once_src file="main/include_js.tpl" src="addons/product_options/js/func.js"}
