{assign var="columns" value="6"}
{if $columns lt 1}{assign var='columns' value=1}{/if}

<script type="text/javascript">
var lblBuy              = "{$lng.lbl_buy|escape:javascript}";
var lblOrMore           = "{$lng.lbl_or_more|escape:javascript}";
var lblPayOnly          = "{$lng.lbl_pay_only|escape:javascript}";
var lblPerItem          = "{$lng.lbl_per_item|escape:javascript}";
var lblYouSave          = "{$lng.lbl_you_save|escape:javascript}";
var lblOr               = "{$lng.lbl_or|escape:javascript}";
var currencySymbol      = "{$config.General.currency_symbol|escape:javascript}";
var alterCurrencySymbol = "{$config.General.alter_currency_symbol|escape:javascript}";
var lblNoItemsAvailable = "{$lng.lbl_no_items_available|escape:javascript}";
var txtItemsAvailable   = "{$lng.txt_items_available|escape:javascript}";
var txtOutOfStock       = "{$lng.txt_out_of_stock|strip_tags|escape:javascript}";
var exceptionMsg        = "{$lng.txt_exception_warning|strip_tags|escape:javascript}!";
var exceptionMsgHtml    = "{$lng.txt_exception_warning|escape:javascript}!";

var maxSelectorQuantity = {$config.Appearance.max_select_quantity|default:"0"};
var alterCurrencyRate   = {$config.General.alter_currency_rate|default:"0.00"};
var useAlertMessages    = '{$use_alert_messages|default:"N"}';

var columns = {$columns|default:1};

var accessoriesTotalPrice = new Number(0.00);
function change_amount(id, val) {ldelim}

	if ($('form[name="order_form"]').children('input[name="product_recommended[' + id + '][amount]"]').length)
		$('form[name="order_form"]').children('input[name="product_recommended[' + id + '][amount]"]').val(val);
{rdelim}

function check_recomended(el, name, id) {ldelim}
	var form = $('form[name="order_form"]');

	if (form)  {ldelim}

		if ($(el).prop('checked')) {ldelim}
			form.append('<input type="hidden" value="' + id + '" name="'+name+'[' + id + '][type]">');
			form.append('<input type="hidden" value="' + id + '" name="'+name+'[' + id + '][product_id]">');
			form.append('<input type="hidden" value="' + id + '" name="'+name+'[' + id + '][add]">');

            var amount = $('[name="'+name+'['+id+'][amount]"]').val();
			form.append('<input type="hidden" value="' + amount + '" name="'+name+'[' + id + '][amount]">');

			if ($('input[name="'+name+'['+id+'][price]"]').length) {ldelim}
				var price = $('input[name="'+name+'['+id+'][price]"]').val();
                accessoriesTotalPrice += parseFloat(price);
				form.append('<input type="hidden" value="' + price + '" name="'+name+'[' + id + '][price]">');
			{rdelim}
		{rdelim}
		else {ldelim}
            accessoriesTotalPrice -= parseFloat($('input[name="'+name+'['+id+'][price]"]').val());
			form.children('input[name*="'+name+'[' + id + ']"]').remove();
		{rdelim}
        accessoriesTotalPrice = Number(accessoriesTotalPrice.toFixed(2));
        accessoriesUpdateTotal();
	{rdelim}
{rdelim}

{literal}
function accessoriesUpdateTotal() {
    if (accessoriesTotalPrice>0) {
        if (!$('#accessories_price').length) {
            $('.our_price').append('<div id="accessories_price"></div>');
        }
        $('#accessories_price').html('<span> + '+currencySymbol+accessoriesTotalPrice+' accessories</span>');
    } else {
        $('#accessories_price').remove();
    }
}
{/literal}
</script>

{if $columns gt 2}
<style>
.acc_product .product_field label {ldelim}
    display: none;
{rdelim}
</style>
{/if}

{if $product_list_name eq ""}{assign var="product_list_name" value="products_list"}{/if}

{* calc cell width optimized for row width 900px and cell margin 5px, padding 5px and border 1px *}
{math assign="cell_width" equation="floor((1280+2*x-20*2*x)/x)" x=$columns}

<div class='acc_row'>
{counter name='acc_products' print=false start=0}
{foreach from=$products item="product" name=acc_products key=k}
    {assign var="product_details_link" value="index.php?target=product&amp;product_id=`$product.product_id`"}
    {cycle values=", cycle" reset=true}
    {counter name='acc_products' print=false assign='acc_products'}

    <input type='hidden' name='{$product_list_name}[{$product.product_id}][product_id]' value='{$product.product_id}' />
    <input type='hidden' name='{$product_list_name}[{$product.product_id}][type]' value='{$product_list_name}' />
    <div style='width:{$cell_width}px;' class='acc_product'>
    <div class='acc_product_main'>
        {if $config_display_thumbnail eq "Y"}
        <div class='acc_thumbnail' {if $columns gt 1}style='width: 100%;'{/if}>
            <center>
                <a href="{$product_details_link}">
                    {include file='common/product_image.tpl' image=$product.image_thumb product_id=$product.product_id id="`$product_list_name`_product_image_small_`$product.product_id`" html_width=100 html_height=150 keep_file_h2w="N"} 
                </a>
            </center>
        </div>
        {/if}
        <div class="fields">
            <div class="product_field{cycle values=", cycle"} link_to">
                <a href="{$product_details_link}">{$product.product|truncate:20}</a>
            </div>
            {*if $config_display_productcode eq "Y"}
            <div class="product_field{cycle values=", cycle"} sku_number">
                <label>{$lng.lbl_part_number}:</label>
                <span id="{$product_list_name}_product_sku_{$product.product_id}">{$product.productcode}</span>
            </div>
            {/if*}


{* this hasn't got any sense in this addon *}
{* the addon have to be fully re-done before this fix *}
            {*if $product.manufacturer && $config_display_manufacturer eq "Y"}
            <div class="product_field{cycle values=", cycle"}">
                <label>{$lng.lbl_manufacturer}:</label>
                {$product.manufacturer}
                {if $product.manufacturer_web}
                <a href="{$product.manufacturer_web}">{$lng.lbl_manufacturer_web_product}</a>
                {/if}
            </div>
            {/if*}
{*
            {if $product.weight ne "0.00" && $config_display_weight eq "Y"}
            <div class="product_field{cycle values=", cycle"} weight">
                <label>{$lng.lbl_weight}:</label>
                <span id="{$product_list_name}_product_weight_{$product.product_id}">
                    {$product.weight|formatprice} {$config.General.weight_symbol}</span>
            </div>
            {/if}

            {if $config_display_qty_in_stock eq "Y" && $config.Appearance.show_in_stock eq "Y" and $product.distribution eq ""}
            <div class="product_field{cycle values=", cycle"}">
                <label>{$lng.lbl_in_stock}:</label>
                     <span id="{$product_list_name}_product_quantity_in_stock_{$product.product_id}">
                      {if $product.avail gt 0}
                        {$lng.txt_items_available|substitute:"items":$product.avail}
                      {else}
                        {$lng.lbl_no_items_available}
                      {/if}
                    </span>
            </div>
            {/if}
*}
{* kornev, it cannot be done this way and have to be fixed *}
            {*if $config_display_options eq "Y" && $product.product_options}
              {include file="addons/accessories/customer_options.tpl"
                product_options=$product.product_options
                product_options_ex=$product.product_options_ex
                input_field_name="`$product_list_name`_options"
                variants=$product.variants
                product_wholesale=$product.product_wholesale
                product=$product
                product_list_name=$product_list_name
                product_id=$product.product_id}
            {/if*}






          {if $config_display_wholesale eq "Y"}
            <div class="product_field{cycle values=", cycle"}" id="{$product_list_name}_product_wholesale_prices_{$product.product_id}"></div>
          {/if}



        </div>
     <div class="clear"></div>
     </div>

     <div class="acc_qty">
            {if $config_display_qty_selector eq "Y"}
            <div class="product_field{cycle values=", cycle"}">
                <label>{$lng.lbl_quantity}</label>
                    {include file="addons/accessories/product_amount.tpl"
                      id="`$product_list_name`_product_quantity_selector_`$product.product_id`"
                      product=$product
                      product_options=$product.product_options
                      amount_field_name="`$product_list_name`[`$product.product_id`][amount]"
                      product_list_name=$product_list_name}
            </div>
            {else}
              <input type="hidden" name="{$product_list_name}[{$product.product_id}][amount]" value="{if $product.min_amount gte 1}{$product.min_amount}{else}1{/if}" />
            {/if}
      </div>


            {if $config_display_price eq "Y"}
            <div class="product_field taxed_price {cycle values=", cycle"}">
                      {if $product.taxed_price ne 0 || $product.variant_price_not_empty}
                        <input type="hidden" name="{$product_list_name}[{$product.product_id}][price]" value="{$product.taxed_price}" />
                        <span class="price" id="{$product_list_name}_product_price_{$product.product_id}">
                          {include file="common/currency.tpl" value=$product.taxed_price plain_text_message=true}
                        </span>
                        <span class="price" id="{$product_list_name}_product_alt_price_{$product.product_id}">
                          {include file="common/alter_currency_value.tpl" alter_currency_value=$product.taxed_price plain_text_message=true}
                        </span>
                        {if $product.taxes}{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}{/if}
                      {else}
                        <input type="text" size="7" name="{$product_list_name}[{$product.product_id}][price]" />
                      {/if}
             </div>
            {/if}

            {*if $addons.estore_products_review}
              <div class="info_box">
              {include file='addons/estore_products_review/product_rating.tpl' rating=$product.rating}
              </div>
            {/if*}

    <!-- accessories add to cart -->
    <div class='acc_add_wrapper'>
        <div class='acc_add'>
			{if $product.product_type eq 4}
				<a href="index.php?target=catalog_redirect&product_id={$product.product_id}">
					<div class="acc_add_inner">
						  <span >{$lng.lbl_catalog_product_button}</span>
					</div>
				</a>
			{else}
				<div class="acc_add_inner">
             	  	<span class="left">{$lng.lbl_add_to_cart}</span> <input type="checkbox" name="{$product_list_name}[{$product.product_id}][add]" id="{$product_list_name}_add_to_cart_{$product.product_id}" value="{$product.product_id}" onchange="check_recomended(this,'{$product_list_name}','{$product.product_id}');" />&nbsp;
				</div>
			{/if}
        </div>
    </div>

    </div>


    {*if $acc_products mod $columns == 0}
        <div class="clear"></div>
        </div>
        <div class='acc_row'>
    {/if*}
{/foreach}
<div class="clear"></div>
</div>

