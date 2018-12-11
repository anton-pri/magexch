{jstabs}
default_tab={$js_tab|default:"sub_det"}

[sub_det]
title={$lng.lbl_product_details}
template="main/products/product/details.tpl"

[sub_det_add]
title={$lng.lbl_additional_information}
template="main/products/product/details_add.tpl"
{/jstabs}

{include file='main/select/edit_lng.tpl' script="index.php?target=`$current_target`&mode=details&product_id=`$product_id`"}
{capture name=section}
{if $current_area eq 'V'}
<form action="index.php?target={$current_target}" method="post" name="product_modify_form" id="product_modify_form" enctype="multipart/form-data">
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="action" value="product_modify" />
<input type="hidden" name="ge_id" value="{$ge_id}" />
{if $product.product_id}
<input type="hidden" name="product_data[product_type]" value="{$product.product_type}" />
{/if}

{include file='tabs/js_tabs.tpl' group="sub"}

<div id="sticky_content" class="buttons">
{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('product_modify_form')" acl=$page_acl}
</div>
</form>
{else}
{include file='tabs/js_tabs.tpl' group="sub"}
{/if}
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_product_details}

<script type="text/javascript">
	{literal}
	$(document).ready(function(){
		var product_modify_form = $("#product_modify_form");
		product_modify_form.validate();
	});
	{/literal}
</script>
