{include_once_src file='main/include_js.tpl' src='js/popup_product.js'}
<div class="form-group row remove-margin-b">
{if $psw_multiple eq 1}

	{if $psw_products ne '' && $psw_products|@count gt 0}
		{foreach from=$psw_products item=psw_product key=psw_index name=psw_product_item}

		{assign var="psw_product_id_id" value="`$psw_prefix_id`_id_item_`$psw_index`"}
		{assign var="psw_product_name_id" value="`$psw_prefix_id`_name_item_`$psw_index`"}
		{assign var="psw_product_amount_id" value="`$psw_prefix_id`_qty_item_`$psw_index`"}

		<tr>
		<td{if $smarty.foreach.psw_product_item.first} id="{$psw_prefix_id}_box_1"{/if}{if $psw_colspan} colspan="{$psw_colspan}"{/if}>
		{if $psw_hide_id_field eq 1}
		<input type="hidden"
                        class="form-control" 
			   name="{$psw_prefix_name}[{$psw_index}][id]"
			   id="{$psw_prefix_id}_id_item_{$psw_index}"
			   value="{$psw_product.id}"/>
		{else}
		<div class="col-xs-2 remove-padding-l">
		<input type="text" size="7"
                        class="form-control" 
			   name="{$psw_prefix_name}[{$psw_index}][id]"
			   id="{$psw_prefix_id}_id_item_{$psw_index}"
			   value="{$psw_product.id}"
			   readonly="readonly" class='micro' title="{$lng.lbl_product_id}" />
		</div>
		{/if}
		<div class="col-xs-6">
		<input type="text" size="39"
                        class="form-control" 
			   name="{$psw_prefix_name}[{$psw_index}][name]"
			   id="{$psw_prefix_id}_name_item_{$psw_index}"
			   value="{$psw_product.name|escape}"
			   {if $psw_use_ajax eq 0}readonly="readonly"{else}placeholder="{$lng.lbl_enter_product_name}..."{/if}
			   title="{$lng.lbl_product_name}" />
        </div>
		{if $psw_amount_name}
		<div class="col-xs-2">
		<input type="text" size="5"
                        class="form-control" 
			   name="{$psw_prefix_name}[{$psw_index}][quantity]"
			   id="{$psw_prefix_id}_qty_item_{$psw_index}"
			   value="{$psw_product.quantity|escape}"
			   class='micro' title="{$lng.lbl_amount}" />
		</div>
		{/if}

		{if $psw_extra_code_tpl ne ''}
			{include file="$psw_extra_code_tpl"}
		{/if}
		<div class="col-xs-2">
		<input type="button"
				class="form-control"
			   id="item_{$psw_index}"
			   value="{$lng.lbl_browse_|strip_tags:false|escape}"
			   onclick="javascript:psw_popup_products(this, '{$psw_prefix_id}_id_', '{$psw_prefix_id}_name_', '{if $psw_amount_name}{$psw_prefix_id}_qty_{/if}', ['{$psw_cat_id}', '{$psw_supplier_id}']);"
		/>
		</div>
		</td>

		{if $smarty.foreach.psw_product_item.first}
		<td id="{$psw_prefix_id}_add_button">
			{include file="main/multirow_add.tpl" mark=$psw_prefix_id is_lined=true}
            <a href="javascript: void(0);" onclick="$(this).closest('tr').find('input[type=hidden],input[type=text]').val('');"><img src="{$ImagesDir}/admin/minus.png" /></a>
		</td>
		{else}
		<td>
			<a href="javascript: void(0);" onclick="$(this).closest('tr')[0].remove();"><img src="{$ImagesDir}/admin/minus.png" /></a>
		</td>
		{/if}
		</tr>

		{if $psw_use_ajax}
		<script type="text/javascript">
			{literal}
			$(document).ready(function() {
				{/literal}
				psw_ready("#{$psw_product_id_id}", "#{$psw_product_name_id}", "#{$psw_product_amount_id}");
				{literal}
			});
			{/literal}
		</script>
		{/if}

		{/foreach}

		{if $psw_use_ajax}
		<script type="text/javascript">
			{literal}
			$(document).ready(function() {
				$('body').bind('event_add_inputset_row', psw_add_inputset_row);
			});
			{/literal}
		</script>
		{/if}

	{/if}

{else}

	{assign var="psw_product_id_id" value="`$psw_prefix_id`newproduct_id"}
	{assign var="psw_product_name_id" value="`$psw_prefix_id`newproduct_id_name"}
	{assign var="psw_product_amount_id" value="$psw_amount_name"}

	{if $psw_hide_id_field eq 1}
	<input type="hidden"
                 class="form-control" 
		   name="{$psw_prefix_name}{$psw_name_id|default:'newproduct_id'}"
		   id="{$psw_prefix_id}newproduct_id"
		   value="{$psw_products.id}" />
	{else}
	<div class="col-xs-2">
	<input type="text" size="7"
                 class="form-control" 
		   name="{$psw_prefix_name}{$psw_name_id|default:'newproduct_id'}"
		   id="{$psw_prefix_id}newproduct_id"
		   value="{$psw_products.id}"
		   readonly="readonly" class='micro' title="{$lng.lbl_product_id}" />
	</div>
	{/if}
	<div class="col-xs-6">
	<input type="text" size="39"
                 class="form-control" 
		   name="{$psw_prefix_name}{$psw_name_name|default:'newproduct_id_name'}"
		   id="{$psw_prefix_id}newproduct_id_name"
		   value="{$psw_products.name|escape}"
		   {if $psw_use_ajax eq 0}readonly="readonly"{else}placeholder="{$lng.lbl_enter_product_name}..."{/if}
		   title="{$lng.lbl_product_name}" />
	</div>
	{if $psw_amount_name}
	<div class="col-xs-2">
	<input type="text" size="5"
                 class="form-control" 
		   name="{$psw_amount_name}"
		   id="{$psw_amount_name}"
		   value="{$psw_products.quantity|escape}"
		   class='micro' title="{$lng.lbl_amount}" />
	</div>
	{/if}

	{if $psw_extra_code_tpl ne ''}
		{include file="$psw_extra_code_tpl"}
	{/if}
	<div class="col-xs-2">
	{if $psw_without_form eq 1}
	<input type="button"
                 class="btn btn-minw btn-default" 
		   value="{$lng.lbl_browse_|strip_tags:false|escape}"
		   onclick="javascript:popup_products('{$psw_prefix_id}newproduct_id', '{$psw_prefix_id}newproduct_id_name', '{if $psw_amount_name}{$psw_amount_name}{/if}', ['{$psw_cat_id}', '{$psw_supplier_id}']);" />
	{else}
	<input type="button"
                 class="btn btn-minw btn-default" 
		   value="{$lng.lbl_browse_|strip_tags:false|escape}"
		   onclick="javascript:popup_products('{$psw_form}.{$psw_prefix_name}{$psw_name_id|default:'newproduct_id'}', '{$psw_form}.{$psw_prefix_name}{$psw_name_name|default:'newproduct_id_name'}', '{if $psw_amount_name}{$psw_form}.{$psw_amount_name}{/if}', ['{$psw_cat_id}', '{$psw_supplier_id}']);" />
	{/if}
	</div>
	{if $psw_use_ajax}
		<script type="text/javascript">
			{literal}
			$(document).ready(function() {
				{/literal}
				psw_ready("#{$psw_product_id_id}", "#{$psw_product_name_id}", "#{$psw_product_amount_id}");
				{literal}
				$('body').bind('event_add_inputset_row', psw_add_inputset_row);
			});
			{/literal}
		</script>
	{/if}

{/if}
</div>
{if $psw_use_ajax}
<style>
	{literal}
	.search_match{color: #779523; font-weight: bold;}
	{/literal}
</style>
{/if}
