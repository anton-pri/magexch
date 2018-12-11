{select_categories category_id=$current_category.category_id assign='subcategories'}

<div class="category_info">
    <h2 class="title">{$current_category.category}</h2>
    {if !$current_category.image.is_default}<div class="image">{include file='common/thumbnail.tpl' image=$current_category.image}</div>{/if}

    {if $current_category.description}<div class="descr">{$current_category.description}</div>{/if}
</div>
<div class="clear"></div>

{*include file='customer/special_sections/hot_deals_week.tpl'*}

{include file='customer/products/featured.tpl'}

{if $products}
    {include file='customer/products/products_top.tpl'}
    <div class="tab_general_content">
    {include file='common/navigation.tpl'}
{if $set_view eq 0}{assign var=product_list_template value=products_gallery}{/if}
    {include file="customer/products/`$product_list_template`.tpl" hidefc='Y'}
    {include file='common/navigation.tpl'}
    </div>
{/if}

{if $subcategories}
<div class="sub_links">
{*$subcategories|@debug_print_var*}
{foreach from=$subcategories item=subcat}
<div class="featured_category">
<div class="white_bg">
    <div class="f_image"><a href="{pages_url var="index" cat=$subcat.category_id}">{include file='common/thumbnail.tpl' image=$subcat.image}</a></div>
    <a class="cat" href="{pages_url var="index" cat=$subcat.category_id}">{$subcat.category}{*if $subcat.product_count && $config.Appearance.count_products} ({$subcat.product_count}){/if*}</a>
</div>
</div>
{/foreach}
</div>
<div class="clear"></div>
{capture name=section_title}{$lng.lbl_more_choices|substitute:category:$current_category.category}{/capture}
{/if}

{include file='customer/special_sections/bottom.tpl'}
