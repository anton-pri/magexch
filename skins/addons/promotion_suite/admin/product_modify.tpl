{* CartWorks.com - Promotion Suite *}
{include_once_src file='main/include_js.tpl' src='js/popup_product.js'}
{include_once_src file='main/include_js.tpl' src='addons/promotion_suite/js/admin.js'}
{include_once file='categories_ajax/include_js.tpl'}

{capture name=block}
<form method="post" name="bundles_form">

<input type="hidden" name="product_id" value="{$product_id}" />
<input type="hidden" name="action" value="ps_bundle_update" />

<div class="input_field form-inline">
       {$lng.lbl_buy_together_select}
	<div class="form-group"><input  class="form-control" type="text" class='micro' size="10" name="discount" value="{$product_offer.bonuses.D.discount}" /></div>
	<div class="form-group">
	<select name="disctype" class="form-control">
		<option value="2" {if $product_offer.bonuses.D.disctype eq "2"} selected="selected"{/if}>{$lng.lbl_percent}, %</option>
        <option value="1" {if $product_offer.bonuses.D.disctype eq "1"} selected="selected"{/if}>{$lng.lbl_absolute}, {$config.General.currency_symbol}</option>
	</select>
	</div>
</div>
	<table class="table table-striped dataTable vertical-center" width="100%">
	<thead>
		<tr>
		<th width="15"><input type='checkbox' class='select_all' class_to_select='products_item' /></th>
		<th width="15%">{$lng.lbl_product_id}</th>
		<th colspan="2" width="*">{$lng.lbl_product}</th>
		</tr>
	</thead>
		{if $product_offer.conditions.P.products}

		{foreach from=$product_offer.conditions.P.products item=v key=pid}
		<tr{cycle values=', class="cycle"'}>
		<td><input type="checkbox" value="Y" name="del_cond[{$pid}]" class='products_item' /></td>
		<td>#{$pid}</td>
		{tunnel func='cw_product_get' id=$pid info_type=0 assign='prod'}
 		<td colspan="2"><a href="product.php?product_id={$pid}" class="ItemsList">{$prod.product}</a></td>
		</tr>
		{/foreach}

		{else}
		<tr>
		<td colspan="4" align="center">{$lng.lbl_no_products}</td>
		</tr>
		{/if}
	</table>
	<table width="100%" class="table dataTable">
		<tr>
		<th colspan="4">{$lng.lbl_add_new}</th>
		</tr>

		{product_selector multiple=1 prefix_name='bundle' prefix_id='ps_prods'}

	</table>

<div class="buttons">
{include file="admin/buttons/button.tpl" href="javascript: cw_submit_form('bundles_form');" button_title=$lng.lbl_update|escape acl=$page_acl style="btn-green push-20"}
</div>

</form>
{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_discount content=$smarty.capture.block}
{* CartWorks.com - Promotion Suite *}
