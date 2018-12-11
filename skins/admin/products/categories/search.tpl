<div class="form-horizontal">
<form action="index.php?target={$current_target}" method="post" name="search_category_form">
<input type="hidden" name="action" value="search" />
<input type="hidden" name="cat" value="{$current_category.category_id}" />
{*capture name=block*}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_substring}</label>
    <div class="col-md-4 col-xs-12"><input type="text" name="posted_data[substring]" value="{$search_prefilled.substring|stripslashes|escape}" class="form-control" /></div>
</div>
<div class="form-group">
    <div class="col-xs-12">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_search href="javascript: cw_submit_form('search_category_form');" style="btn-green"}</div>
</div>

{*/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_search content=$smarty.capture.block*}

</form>
</div>


{include file='common/navigation.tpl'}
{include file='admin/products/categories/list.tpl' subcategories=$categories mode='search' js_tab='category_search' process_category_form=process_category_search_form}
