{capture name=section}
<form action="index.php?target={$current_target}" method="post" name="add_product_form" id="add_product_form" enctype="multipart/form-data">
<input type="hidden" name="section" value="main" />
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="action" value="add" />

{include file="main/products/product/details.tpl"}

{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_product href="javascript: cw_submit_form(document.add_product_form);" style="btn-green push-20"}
</form>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_add_product}


<script type="text/javascript">
	{literal}
	$(document).ready(function(){
		var add_product_form = $("#add_product_form");
		add_product_form.validate();
	});
	{/literal}
</script>
