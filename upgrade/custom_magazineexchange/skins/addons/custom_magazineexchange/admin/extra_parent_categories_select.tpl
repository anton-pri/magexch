<div class="input_field_1 right"><label>{$lng.lbl_extra_parent_categories}:</label>
{tunnel func='magexch_get_extra_categories' via='cw_call' param1=$current_category.category_id assign='extra_categories'}
    {include file='main/select/category.tpl' name='cat_parents[]' value=$extra_categories multiple=true disabled=$read_only}
</div>
