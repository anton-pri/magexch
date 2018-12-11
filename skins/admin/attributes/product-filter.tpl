{if $attribute.attribute_id}

{include file='common/subheader.tpl' title=$lng.lbl_product_filter}

<form action="index.php?target={$current_target}&mode=att" method="post" name="attribute_pf_modify_form" class="form-horizontal">
<input type="hidden" name="action" value="product_filter" />
<input type="hidden" name="attribute_id" value="{$attribute.attribute_id}">

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_use_in_product_filter}
    <input type="hidden" name="posted_data[pf_is_use]" value="0" />
    <input type="checkbox" name="posted_data[pf_is_use]" value="1" {if $attribute.pf_is_use}checked{/if} />
    </label>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_product_filter_orderby}</label>
    <div class="col-xs-6 col-md-3"><input type="text" class="form-control" name="posted_data[pf_orderby]" value="{$attribute.pf_orderby}" /></div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_product_filter_display_type}</label>
    <div class="col-xs-12">{include file='admin/attributes/product-filter-display-type.tpl' name="posted_data[pf_display_type]" value=$attribute.pf_display_type type=$attribute.type}</div>
</div>

<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_facet}
		<input type="checkbox" name="posted_data[facet]" value="1" {if $attribute.facet}checked{/if} />
	</label>
</div>

<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_attr_description}</label>
	<div class="col-xs-12"><textarea  class="form-control" name="posted_data[description]">{$attribute.description}</textarea></div>
</div>

{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('attribute_pf_modify_form')" style="btn-green push-20"}
</form>

{/if}
