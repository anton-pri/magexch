{capture name=block}
<div class="form-horizontal">

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[tags]" />{/if}
        {$lng.lbl_tags}:
    </label>
	<div class="col-xs-12 col-md-4"><input class="form-control" type="text" id="product_tags" name="product_data[tags]" value="{$product.list_tags|escape:"html"}" {if $read_only}disabled{/if}/></div>
</div>

<div class="form-group">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[pdf_link]" />{/if}
        {$lng.lbl_pdf_acrobat}:
    </label>
    <div class="col-xs-12 col-md-4">
      <input class="form-control" type="text" name="product_data[pdf_link]" value="{$product.pdf_link}" {if $read_only}disabled{/if}/>
    </div>
    <div  class="col-xs-12 col-md-4">
      {if !$read_only}
      <div class="additional_field">
      <input type="file" name="pdf_file" >
      </div>
      {/if}
    </div>
</div>

{if $addons.egoods}
{include file='addons/egoods/egoods.tpl'}
{/if}

<div class="form-group">
	<label class='multilan col-xs-12'>
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[descr]" />{/if}
        {$lng.lbl_short_description} 
    </label>
    <div class="col-xs-12"><textarea class="form-control" name="product_data[descr]" rows="15" cols="80"{if $read_only} disabled{/if}>{$product.descr}</textarea></div>
	{*include file='main/textarea.tpl' name='product_data[descr]' data=$product.descr disabled=$read_only init_mode='exact'*}
</div>

<div class="form-group">
    <label class='multilan col-xs-12'>
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[features_text]" />{/if}
        {$lng.lbl_features}: 
    </label>
    <div class="col-xs-12">{include file='main/textarea.tpl' name="product_data[features_text]" data=$product.features_text disabled=$read_only init_mode='exact' no_wysywig='Y'}</div>
</div>

<div class="form-group">
    <label class='multilan col-xs-12'>
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[specifications]" />{/if}
        {$lng.lbl_specifications}: 
    </label>
    <div class="col-xs-12">{include file='main/textarea.tpl' name="product_data[specifications]" data=$product.specifications width="80%" btn_rows=4 disabled=$read_only  init_mode='exact'}</div>
</div>

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[low_avail_limit]" />{/if}
        {$lng.lbl_lowlimit_in_stock}
    </label>
	<div class="col-xs-12 col-md-4"><input class="form-control" type="text" name="product_data[low_avail_limit]" value="{if $product.product_id eq ""}10{else}{ $product.low_avail_limit }{/if}" {if $read_only}disabled{/if}/></div>
</div>

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[min_amount]" /></label>{/if}
        {$lng.lbl_min_order_amount}
    </label>
	<div class="col-xs-12 col-md-4"><input class="form-control" type="text" name="product_data[min_amount]" value="{if $product.product_id eq ""}1{else}{$product.min_amount}{/if}" {if $read_only}disabled{/if}/></div>
</div>

<div class="form-group dimension">
    <label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[weight]"{if $is_variants eq 'Y'} disabled{/if} />{/if}
        {$lng.lbl_dimension}
    </label>
    <div class="col-xs-12 form-inline">
      {if $is_variants eq 'Y'}
        <b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
      {else}
        <div class="form-group push-10-r"><span>{$lng.lbl_width}: <input class="form-control" type="text" name="product_data[dim_x]" size="8" value="{$product.dim_x}" {if $read_only}disabled{/if}/></span></div>
        <div class="form-group push-10-r"><span>{$lng.lbl_height}: <input class="form-control" type="text" name="product_data[dim_y]" size="8" value="{$product.dim_x}" {if $read_only}disabled{/if}/></span></div>
        <div class="form-group"><span>{$lng.lbl_depth}: <input class="form-control" type="text" name="product_data[dim_z]" size="8" value="{$product.dim_x}" {if $read_only}disabled{/if}/></span></div>
      {/if}
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_statistics}</label>
    <div class="col-xs-12">
      <span style="font-weight: bold; width: 150px; float: left;">{$lng.lbl_views}</span>
      <span style="font-weight: bold; width: 150px; float: left;">{$lng.lbl_purchases}</span>
      <span style="font-weight: bold; width: 150px; float: left;">{$lng.lbl_deletions_from_cart}</span>
      <span style="font-weight: bold;">{$lng.lbl_additions_to_cart}</span><br>
      <span style="width: 150px; float: left;">{$product.views_stats}</span>
      <span style="width: 150px; float: left;">{$product.sales_stats}</span>
      <span style="width: 150px; float: left;">{$product.del_stats}</span>
      <span>{$product.add_to_cart}</span>
    </div>
</div>

{if $addons.RMA}
<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[return_time]" />{/if}
        {$lng.lbl_return_time}
    </label>
	<div class="col-xs-12 col-md-4"><input class="form-control" type="text" name="product_data[return_time]" value="{$product.return_time}" {if $read_only}disabled{/if}/></label></div>
</div>
{/if}

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[membership_ids]" />{/if}
        {$lng.lbl_membership}
    </label>
    <div class="col-xs-12 col-md-4">
      {if $mode eq 'add' && !$product.membership_ids}
      {include file="admin/select/membership.tpl" value=$product.membership_ids name="product_data[membership_ids][]" multiple=true retail_selected=true disabled=$read_only}
      {else}
      {include file="admin/select/membership.tpl" value=$product.membership_ids name="product_data[membership_ids][]" multiple=true disabled=$read_only}
      {/if}
    </div>
</div>

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[free_tax]" />{/if}
        {$lng.lbl_tax_exempt}
    </label>
    <div class="col-xs-12 col-md-4">
	  <select class="form-control" name="product_data[free_tax]"{*if $taxes} onchange="javascript: ChangeTaxesBoxStatus();"{/if*} {if $read_only}disabled{/if}>
		<option value='N'{if $product.free_tax eq 'N'} selected="selected"{/if}>{$lng.lbl_no}</option>
		<option value='Y'{if $product.free_tax eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
	  </select> 
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[free_shipping]" />{/if}
        {$lng.lbl_free_shipping}
    </label>
    <div class="col-xs-12 col-md-4">
	  <select class="form-control" name="product_data[free_shipping]" {if $read_only}disabled{/if}>
		<option value='N'{if $product.free_shipping eq 'N'} selected="selected"{/if}>{$lng.lbl_no}</option>
		<option value='Y'{if $product.free_shipping eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
	  </select>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[shipping_freight]" />{/if}
        {$lng.lbl_shipping_freight} ({$config.General.currency_symbol})
    </label>
	<div class="col-xs-12 col-md-4"><input class="form-control" type="text" name="product_data[shipping_freight]" value="{$product.shipping_freight|formatprice|default:$zero}" {if $read_only}disabled{/if}/></div>
</div>

<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id && !$read_only}<input type="checkbox" value="1" name="fields[discount_avail]" />{/if}
        {$lng.lbl_apply_global_discounts}
	    <input type="checkbox" style="margin-top: 7px;" name="product_data[discount_avail]" value="1"{if $product.product_id eq "" || $product.discount_avail} checked="checked"{/if} {if $read_only}disabled{/if}/>&nbsp;
    </label>

</div>

{if $usertype eq 'A'}
{include file='main/products/product/details_sections.tpl'}
{/if}

</div>

<script type="text/javascript">
    <!--
    {literal}
    $(document).ready(function() {
        $('#product_tags').tagsInput({
            width: 'auto',
            height: '75px'
        });
    });
    {/literal}
    -->
</script>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block title=$lng.lbl_details}

