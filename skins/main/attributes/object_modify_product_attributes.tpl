{capture name="section"}
<form action="index.php?target={$current_target}" method="post" name="products_attributes_form">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="action" value="attributes_modify" />
<input type="hidden" name="ge_id" value="{$ge_id}" />
<div class="form-horizontal">
{include file='admin/attributes/object_modify.tpl' hide_subheader="Y"}
<div class="buttons"><input type="submit" value="{$lng.lbl_save}" class="btn btn-green push-20" /></div>
</div>
</form>

{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_attributes content=$smarty.capture.section}
