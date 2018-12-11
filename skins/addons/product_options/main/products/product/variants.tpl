{capture name=section}

<div class="dialog_title">{$lng.txt_product_variants_note_2}</div>

{if $product_options}
<div align="right"><div class="visible_plus">{include file='main/visiblebox_link.tpl' mark='boxfpv' title=$lng.lbl_filter_product_variants}</div></div>

<form action="index.php?target={$current_target}" method="post" name="product_variants_search_form">
<input type="hidden" name="mode" value="details" />
<input type="hidden" id="imageW_onunload" name="imageW_onunload" value="" />
<input type="hidden" name="section" value="variants" />
<input type="hidden" name="action" value="product_variants_search" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />

<div style="display: none;" id="boxfpv">
{include file='common/subheader.tpl' title=$lng.lbl_filter_product_variants}
<p>{$lng.txt_filter_product_variants_note}</p>
{foreach from=$product_options item=v}
    {if $v.type eq ''}
<div class="input_field_0">
	<label>{$v.name}&nbsp;
{assign var="product_option_id" value=$v.product_option_id}
        <input type='checkbox' class='select_all' class_to_select='product_option_item_{$product_option_id}' title="{$lng.lbl_select_all}" />
    </label>
       <div class="width570px float-left">
	{foreach from=$v.options item=o}
{assign var="option_id" value=$o.option_id}
{assign var="tmp_class" value=$search_variants[$product_option_id]}
	<label><input type="checkbox" name="search[{$product_option_id}][{$option_id}]" value="{$option_id}"{if $tmp_class[$option_id] ne '' || $is_search_all eq 'Y'} checked="checked"{/if} class='product_option_item_{$product_option_id}' />&nbsp;{$o.name}</label>&nbsp;&nbsp;
	{/foreach}
       </div>
</div>
    {/if}
{/foreach}
</table>
<input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
</div>
</form>
{/if}
<br />
<br />

{if $def_variant_failure}
<div class="field_error">{$lng.lbl_warning}: {$lng.txt_default_variant_failure_note}</div>
{/if}

<form action="index.php?target={$current_target}" method="post" name="product_variants_form">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="action" value="product_variants_modify" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />

{if $ge_id}<b>* {$lng.lbl_note}:</b> {$lng.txt_edit_product_group}{/if}
<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    {if $ge_id}<th width="15">&nbsp;</th>{/if}
	{if !$read_only}<th width="15"><input type='checkbox' class='select_all' class_to_select='product_variants_item' title="{$lng.lbl_select_all}" /></th>{/if}
	<th>{$lng.lbl_variants}</th>
	<th>{$lng.lbl_image}</th>
	<th>{$lng.lbl_sku}</th>
    <th>{$lng.lbl_eancode}</th>
    <th>{$lng.lbl_mpn}</th>
	<th>{$lng.lbl_weight}</th>
    <th>{$lng.lbl_avail}</th>
    <th>{$lng.lbl_price}</th>
    <th>{$lng.lbl_cost}</th>
	<th>{$lng.lbl_def}</th>
    {include file="addons/product_options/main/products/product/variant-extra.tpl" display_header=1}
</tr>
</thead>
{if $variants}
{foreach from=$variants item=v key=k}
<tr{cycle name="classes" values=', class="cycle"'}>
	{if $ge_id}<td><input type="checkbox" value="1" name="fields[variants][{$k}]" /></td>{/if}
	{if !$read_only}<td><input type="checkbox" id="v{$k}" name="vids[{$k}]" value="{$k}" class="product_variants_item" /></td>{/if}
	<td >
	{foreach from=$v.options item=o}
	<div class="input_field_small_0">
		<label class="table_lbl">{$o.option_name}: {$o.name}</label>
		
	</div>
	{/foreach}
	</td>
    <td align="center" >{if $v.image}<a href="{$v.image.tmbn_url}" target="_blank">{include file='common/thumbnail.tpl' image=$v.image class=variant}</a>{/if}</td>
	<td><input class="form-control" type="text" size="8" name="vs[{$k}][productcode]" value="{$v.productcode}"{if $read_only} disabled{/if} /></td>
    <td><input class="form-control" type="text" size="8" name="vs[{$k}][eancode]" value="{$v.eancode}"{if $read_only} disabled{/if} /></td>
    <td><input class="form-control" type="text" size="8" name="vs[{$k}][mpn]" value="{$v.mpn}"{if $read_only} disabled{/if} /></td>
	<td><input class="form-control" type="text" size="4" name="vs[{$k}][weight]" value="{$v.weight|formatprice}"{if $read_only} disabled{/if} /></td>
    <td><input class="form-control" type="text" size="4" name="vs[{$k}][avail]" value="{$v.avail|formatnumeric}" /></td>
    <td align="center">
        {include file="addons/product_options/main/products/product/variant-price.tpl" variant=$v k=$k}
    </td>
    <td>
        <input class="form-control" type="text" size="6" name="vs[{$k}][cost]" id="product_data_{$k}_cost" value="{$v.cost|formatprice}"{if $read_only} disabled{/if} />
    </td>
	<td align="center">
        <input type="radio" name="def_variant" value="{$k}"{if $v.def eq 'Y'} checked="checked"{/if}{if $read_only} disabled{/if} />
    </td>
    {include file="addons/product_options/main/products/product/variant-extra.tpl" variant=$v k=$k}
</tr>
{/foreach}
<thead>
<tr>
    {if $ge_id}<th width="15">&nbsp;</th>{/if}
    {if !$read_only}<th width="15"><input type='checkbox' class='select_all' class_to_select='product_variants_item' title="{$lng.lbl_select_all}" /></th>{/if}
    <th>{$lng.lbl_variants}</th>
    <th>{$lng.lbl_image}</th>
    <th>{$lng.lbl_sku}</th>
    <th>{$lng.lbl_eancode}</th>
    <th>{$lng.lbl_mpn}</th>
    <th>{$lng.lbl_weight}</th>
    <th>{$lng.lbl_avail}</th>
    <th>{$lng.lbl_price}</th>
    <th>{$lng.lbl_cost}</th>
    <th>{$lng.lbl_def}</th>
    {include file="addons/product_options/main/products/product/variant-extra.tpl" display_header=1}
</tr>
</thead>


<tr><td colspan="12"><b>Mass update selected variants</b></td></tr>
<tr>
    {if $ge_id}<th width="15">&nbsp;</th>{/if}
    {if !$read_only}<th width="15">&nbsp;</th>{/if}
    <th colspan="2"><input type="checkbox" value="1" name="set_mass_apply[image]" /> {$lng.lbl_image}</th>
    <th>&nbsp;</th>
    <th><input type="checkbox" value="1" name="set_mass_apply[eancode]" /> {$lng.lbl_eancode}</th>
    <th><input type="checkbox" value="1" name="set_mass_apply[mpn]" /> {$lng.lbl_mpn}</th>
    <th><input type="checkbox" value="1" name="set_mass_apply[weight]" /> {$lng.lbl_weight}</th>
    <th><input type="checkbox" value="1" name="set_mass_apply[avail]" /> {$lng.lbl_avail}</th>
    <th><input type="checkbox" value="1" name="set_mass_apply[price]" /> {$lng.lbl_price}</th>
    <th><input type="checkbox" value="1" name="set_mass_apply[cost]" /> {$lng.lbl_cost}</th>
    <th>&nbsp;</th>
    {include file="addons/product_options/main/products/product/variant-extra.tpl" display_header=1 k='mass_apply'}
</tr>
<tr>
    {if $ge_id}<td>&nbsp;</td>{/if}
    {if !$read_only}<td>&nbsp;</td>{/if}
    <td colspan="2" align="center">
      <input type="text" size="20" name="mass_apply[image]" value=""{if $read_only} disabled{/if} style="width:95%" />
    </td>
    <td>&nbsp;</td>
    <td><input class="form-control" type="text" size="8" name="mass_apply[eancode]" value=""{if $read_only} disabled{/if} /></td>
    <td><input class="form-control" type="text" size="8" name="mass_apply[mpn]" value=""{if $read_only} disabled{/if} /></td>
    <td><input class="form-control" type="text" size="4" name="mass_apply[weight]" value=""{if $read_only} disabled{/if} /></td>
    <td><input class="form-control" type="text" size="4" name="mass_apply[avail]" value="" /></td>
    <td align="center">
        {include file="addons/product_options/main/products/product/variant-price.tpl" k='mass_apply'}
    </td>
    <td>
        <input type="text" size="6" name="mass_apply[cost]" id="product_data_mass_apply_cost" value=""{if $read_only} disabled{/if} />
    </td>
    <td align="center">&nbsp;</td>
    {include file="addons/product_options/main/products/product/variant-extra.tpl" k='mass_apply'}
</tr>


{else}
<tr>
    <td colspan="12" align="center">{$lng.lbl_none}</td>
</tr>
{/if}
</table>
{if $variants}
<div class="product_buttons">
    {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('product_variants_form')" button_title=$lng.lbl_update_delete acl=$page_acl style="btn-danger"}
</div>




    {if !$read_only}
    <div class="push-20">
        {include file='common/subheader.tpl' title=$lng.lbl_edit_selected_images}
    	{include file='admin/images/edit.tpl' delete_js="cw_submit_form('product_variants_form', 'variants_delete_image');" button_name=$lng.lbl_save in_type='products_images_var' acl=$page_acl}
	</div>
	
         <div class="product_buttons bottom">
        {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('product_variants_form')" button_title=$lng.lbl_update acl=$page_acl style="btn-green push-5-r push-20"}
        {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('product_variants_form', 'variants_delete_image')" button_title=$lng.lbl_delete acl=$page_acl style="btn-green push-5-r push-20"}
        </div>
    {/if}
{/if}
</form>

{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_product_variants content=$smarty.capture.section}
