{assign var="variant_href" value="javascript:switchOn('tab_product_variants','contents_product_variants', 'product_variants', '');"}
{assign var="wholesale_href" value="javascript:switchOn('tab_wholesale', 'contents_wholesale', 'wholesale', '');"}
<div class="row">
<div class="col-md-6">
{capture name=block}
{if $ge_id}<b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}{/if}

<div class="form-horizontal">
<a id="thumb"></a>

<div class="form-group">

	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[product_image]" />{/if}
        {$lng.lbl_product_image}
      </label>
      <div class="col-xs-12">
	 {include file='admin/images/edit.tpl' image=$product.image_det delete_js="cw_submit_form('product_modify_form', 'delete_product_image');" button_name=$lng.lbl_change_image idtag="edit_product_image" in_type="products_images_det"}
      </div>
</div>

{if $config.Appearance.show_thumbnails eq "Y"}
<div class="form-group min_thumb">

	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="Y" name="fields[thumbnail]" />{/if}
        {$lng.lbl_thumbnail}
      </label>
      <div class="col-xs-12">
	{include file='admin/images/edit.tpl' image=$product.image_thumb delete_js="cw_submit_form('product_modify_form', 'delete_thumbnail');" button_name=$lng.lbl_change_image in_type="products_images_thumb"}
      </div>
</div>
{/if}

</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block title=$lng.lbl_product_thumbnail}

{capture name=block2}

<div class="form-horizontal">
<a id="class"></a>

{if $config.Appearance.categories_in_products eq '1'}
<div class="form-group main_cat">

    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[category_id]" />{/if}
        {$lng.lbl_main_category}
    </label>
    <div class="col-xs-12">
     {include file='admin/select/category.tpl' name='product_data[category_id]' value=$product.category_id disabled=$read_only}
    </div>
</div>
   
<div class="form-group add_cat">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[category_ids]" />{/if}
        {$lng.lbl_additional_categories}
    </label>
    <div class="col-xs-12">
      {include file='admin/select/category.tpl' name='product_data[category_ids][]' value=$product_categories disabled=$read_only multiple=true}
    </div>
</div>
{/if}

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[status]" />{/if}
        {$lng.lbl_availability}
    </label>
    <div class="col-xs-12 col-md-6">
      {include file='admin/select/availability_product.tpl' name='product_data[status]' value=$product.status}
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[attribute_class_ids]" />{/if}
        {$lng.lbl_attribute_class}
    </label>
    {*include file='main/select/attribute_class.tpl' name='product_data[attribute_class_id]' value=$product.attribute_class_id is_please_select=1*}
    <div class="col-xs-12 col-md-6">
      {include file='admin/select/attribute_classes.tpl' name='product_data[attribute_class_ids][]' values=$product.attribute_class_ids is_please_select=0 multiple=5}
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {$lng.lbl_product_type}
    </label>
    <div class="col-xs-12 col-md-6">
      {include file='admin/select/product_type.tpl' name='product_data[product_type]' value=$product.product_type|default:$last_added_product_type}
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {$lng.lbl_supplier}
    </label>
    <div class="col-xs-12 col-md-6">
      {include file='main/select/select.tpl' name='product_data[supplier]' data=$suppliers field_id='customer_id' field='fullname' value=$product.system.supplier_customer_id is_please_select=true}
    </div>
</div>

</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2 title=$lng.lbl_classification}

{capture name=block3}
<div class="form-horizontal">
<a id="details"></a>

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" disabled />{/if}
        {$lng.lbl_sku}
    </label>
    <div class="col-xs-12 col-md-6">
      <input type="text" name="product_data[productcode]" value="{$product.productcode}" {if $read_only}disabled{/if} class="form-control" />
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" disabled />{/if}
        {$lng.lbl_manufacturer_code}
    </label>
    <div class="col-xs-12 col-md-6">
      <input type="text" name="product_data[manufacturer_code]" value="{$product.manufacturer_code}" {if $read_only}disabled{/if} class="form-control" />
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" disabled />{/if}
        {$lng.lbl_eancode}
    </label>
    <div class="col-xs-12 col-md-6">
      <input type="text" name="product_data[eancode]" value="{$product.eancode}" {if $read_only}disabled{/if} class="form-control" />
      {if $addons.Bar_Code && $product.barcode.file_url}
        <a href="{$product.barcode.file_url}">{$lng.lbl_print}</a>
      {/if}
    </div>
</div>

<div class="form-group required">

	<label class='required multilan col-xs-12'>
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[product]" />{/if}
        {$lng.lbl_product_name} 
       </label>
       <div class="col-xs-12">
	  <input type="text" name="product_data[product]" id="product" value="{$product.product|escape}"{if $read_only} disabled{/if} class="form-control required" />
       </div>
</div>

<div class="form-group required">

	<label class='{if $config.product.product_descr_is_required eq 'Y'}required {/if}multilan col-xs-12'>
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[fulldescr]" />{/if}

        {$lng.lbl_det_description}
    </label>
    <div class="col-xs-12">
	{include file='main/textarea.tpl' name="product_data[fulldescr]" data=$product.fulldescr disabled=$read_only init_mode='exact' class='required'}
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {$lng.lbl_price}  ({$config.General.currency_symbol})
    </label>
<div class="col-xs-12">
<div class="row">

        <div class="col-xs-3 col-md-3">
        <label class="col-xs-12">
        {if $ge_id && !$read_only}{if $is_variants eq 'Y'}<input type="checkbox" disabled />{else}<input type="checkbox" value="1" name="fields[price]" />{/if}{/if}
        {$lng.lbl_price}
    </label>
      {if $is_variants eq 'Y'}
        <b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
      {else}
         <input type="text" name="product_data[price]" id="product_data_price" value="{$product.price|formatprice|default:$zero}"{if $read_only} disabled{/if} class="form-control" />
      {/if}
        </div>

        <div class="col-xs-3 col-md-3">
    <label class="col-xs-12" style="white-space:nowrap">
        {if $ge_id && !$read_only}{if $is_variants eq 'Y'}<input type="checkbox" disabled />{else}<input type="checkbox" value="1" name="fields[list_price]" />{/if}{/if}
        {$lng.lbl_list_price}
    </label>
      <input type="text" name="product_data[list_price]" value="{$product.list_price|formatprice|default:$zero}"{if $read_only} disabled{/if} class="form-control" />
        </div>



	<div class="col-xs-3 col-md-3">
        <label class="col-xs-12">
        {if $ge_id && !$read_only}{if $is_variants eq 'Y'}<input type="checkbox" disabled />{else}<input type="checkbox" value="1" name="fields[cost]" />{/if}{/if}
        {$lng.lbl_cost}
    </label>
      {if $is_variants eq 'Y'}
        <b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
      {else}
         <input type="text" name="product_data[cost]" id="product_data_cost" value="{$product.cost|formatprice|default:$zero}"{if $read_only} disabled{/if} class="form-control" />
      {/if}
	</div>


</div>
</div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" disabled />{/if}
        {if $ge_id && $read_only}{if $is_variants eq 'Y'}<input type="checkbox" disabled />{else}<input type="checkbox" value="1" name="fields[avail]" />{/if}{/if}
        {$lng.lbl_quantity_in_stock}
    </label>
    <div class="col-xs-12">
      {if $is_variants eq 'Y'}
      <b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
      {else}


      <div class="row">
      <div class="col-xs-3 col-md-3">
          <label>{$lng.lbl_avail}</label>
          <input type="text" name="product_data[avail]" value="{$product.avail|default:0}" class="form-control push-5-r" />
      </div>
      <div class="col-xs-3 col-md-3">
          <label>{$lng.lbl_avail_ordered}</label>
          <input type="text" name="product_data[avail_ordered]" value="{$product.avail_ordered|default:0}" class="form-control push-5-r" />
      </div>
      <div class="col-xs-3 col-md-3">
          <label>{$lng.lbl_avail_sold}</label>
          <input type="text" name="product_data[avail_sold]" value="{$product.avail_sold|default:0}" class="form-control push-5-r" />
      </div>
      <div class="col-xs-3 col-md-3">
          <label>{$lng.lbl_avail_reserved}</label>
          <input type="text" name="product_data[avail_reserved]" value="{$product.avail_reserved|default:0}" class="form-control push-5-r" />
      </div>
      </div>
      {/if}
    </div>
</div>

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}
            {if $is_variants eq 'Y'}<input type="checkbox" disabled />{else}<input type="checkbox" value="1" name="fields[weight]" />{/if}
        {/if}
        {$lng.lbl_weight} ({$config.General.weight_symbol})
    </label>
    <div class="col-xs-3 col-md-3">
      {if $is_variants eq 'Y'}
      <b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
      {else}
        <input type="text" name="product_data[weight]" value="{ $product.weight|formatprice|default:$zero }" {if $read_only}disabled{/if} class="form-control" />
      {/if}
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[shippings][]" />{/if}
        {$lng.lbl_available_product_shippings}
    </label>
    <div class="col-xs-12">
      {if $config.Shipping.enable_all_shippings_for_all == 'Y'}
      {assign var='hidden' value= true}
        {$lng.opt_enable_all_shippings_for_all_products}
      {/if}
      {include file='main/select/shipping.tpl' hidden= $hidden values=$product.uns_shippings name='product_data[shippings][]' multiple=true}
    </div>
</div>

</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block3 title=$lng.lbl_details}
</div>

<div class="col-md-6">
{capture name=block4}
<div class="form-horizontal">
{include file='admin/attributes/object_modify.tpl' show_required='Y' hide_subheader='Y'}
</div>

{*include file='admin/products/panel.tpl'*}

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block4 title=$lng.lbl_addons}
</div>
</div>
