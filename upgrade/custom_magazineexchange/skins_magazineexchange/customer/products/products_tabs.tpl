{if $current_magazine_name ne ''}
<h1 class="category-title">{$current_magazine_name}</h1>
{/if}
{if $category_magexch_attributes.magexch_category_type eq "Magazine" || $category_magexch_attributes.magexch_category_type eq "Year"}

{jstabs name='product_data_customer'}
default_tab={$js_tab|default:"1"}
default_template="customer/products/category_tabs.tpl"

[1]
title="{$lng.lbl_back_issues}"

{if ($category_magexch_attributes.magexch_category_tab_content_2 neq 0 || ($vendorid ne '' && $category_magexch_attributes.magexch_category_tab_content_2 neq '')) && $category_magexch_attributes.magexch_category_tab_title_2}
[2]
title="{$category_magexch_attributes.magexch_category_tab_title_2}"
{/if}

{if ($category_magexch_attributes.magexch_category_tab_content_3 neq 0) && $category_magexch_attributes.magexch_category_tab_title_3}
[3]
title="{$category_magexch_attributes.magexch_category_tab_title_3}"
{/if}

{if ($category_magexch_attributes.magexch_category_tab_content_4 neq 0) && $category_magexch_attributes.magexch_category_tab_title_4}
[4]
title="{$category_magexch_attributes.magexch_category_tab_title_4}"
{/if}

{/jstabs}
{include file='tabs/js_tabs.tpl'}
{/if}
