{capture name=section}

<form action="index.php?target={$current_target}" method="post" name="products_prices_form">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="action" value="wholesales_modify" />
<input type="hidden" name="ge_id" value="{$ge_id}" />

{include file='addons/wholesale_trading/product_wholesale_prices.tpl' prefix="wprices'}

{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form(document.products_prices_form);" button_title=$lng.lbl_add_update acl=$page_acl style="btn-green push-20 push-5-r"}
{if $products_prices}
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form(document.products_prices_form,'wholesales_delete');" button_title=$lng.lbl_delete_selected acl=$page_acl style="btn-danger push-20"}
{/if}

</form>
{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_wholesale_prices content=$smarty.capture.section}