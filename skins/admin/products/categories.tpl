{capture name=section}
<div class="block">
<div class="block-content tab-content">
  {include file='admin/products/category/location.tpl'}

  <p>{$lng.txt_categories_management_top_text}</p>

</div>
</div>

<div class="block">
{jstabs name='categories'}
default_tab={$js_tab|default:'categories'}

[categories]
title="{$lng.lbl_modify_category}"
template="admin/products/categories/list.tpl"

[search]
title="{$lng.lbl_search_categories}"
template="admin/products/categories/search.tpl"

[featured_products]
title="{$lng.lbl_featured_products}"
template="admin/products/categories/featured_products.tpl"

[new_arrivals]
title="{$lng.lbl_new_arrivals}"
template="admin/products/categories/new_arrivals.tpl"

{/jstabs}


{include file='admin/tabs/js_tabs.tpl'}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_categories local_config='category_settings'}
