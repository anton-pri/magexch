<section>
<!-- cw@category_select_subcategories [ -->
{select_categories category_id=$current_category.category_id images=1 assign='subcategories'}
<!-- cw@category_select_subcategories ] -->

<!-- cw@category_info [ -->
<div class="category_info">
    {if !$current_category.image.is_default}<div style="float:left;width:{$config.Appearance.categories_images_thumb_width|default:200}px;height:{$config.Appearance.categories_images_thumb_height|default:200}px">{include file='common/product_image.tpl' image=$current_category.image category_id=$current_category.category_id width=$config.Appearance.categories_images_thumb_width|default:200  height=$config.Appearance.categories_images_thumb_height|default:200}</div>{/if}
    <!-- cw@category_title [ -->
    <h1 class="title">{$current_category.category}</h1>
    <!-- cw@category_title ] -->

    {if $current_category.description}<div class="descr">{$current_category.description}</div>{/if}
	{if $product_filter}
	{foreach from=$product_filter item=pf key=pf_key}
		{if $pf.selected}
			{foreach from=$pf.selected item=pfs key=pfs_key}
				{foreach from=$pf.values item=pfv key=pfv_key}
					{if $pfs eq $pfv_key}
						{if $pfv.description}
							{eval var=$pfv.description}
						{else}
							{eval var=$pf.description}
						{/if}
					{/if}
				{/foreach}
			{/foreach}
		{/if}
	{/foreach}
	{/if}
<div class="clear"></div>
</div>
<!-- cw@category_info ] -->

<!-- cw@category_subheader [ -->
<div class="cat_title">{$current_category.category}</div>
<!-- cw@category_subheader ] -->


{if $replaced_url}
	<input id="replaced_url" type="hidden" value="{$replaced_url}" />
{/if}

{*include file='customer/special_sections/hot_deals_week.tpl'*}

{include file='customer/products/featured.tpl'}

{if $products}
<!-- cw@products_top [ -->
    {include file='customer/products/products_top.tpl'}
<!-- cw@products_top ] -->

<!-- cw@products_list [ -->

    <div class="tab_general_content">
    {include file="customer/products/`$product_list_template`.tpl" hidefc='Y'}
    {if $navigation.total_pages gt 2}<div class="nav_bottom">{include file='common/navigation_customer.tpl'}</div>{/if}
    </div>
<!-- cw@products_list ] -->

{/if}

{if $subcategories}
{*capture name=section*}
<!-- cw@subcategories [ -->
<div class="subcategories">
<p class="subcategory-heading">{$lng.lbl_subcategories}</p>
<div class="categories_list">
{foreach from=$subcategories item=subcat}
<div class="subcategory">
    <div class="sub_image"><a href="{pages_url var="index" cat=$subcat.category_id}">{include file='common/product_image.tpl' image=$subcat.image html_width=150 html_height=150 category_id=$subcat.category_id}</a></div>
    <a class="cat" href="{pages_url var="index" cat=$subcat.category_id}">{$subcat.category}{if $subcat.product_count && $config.Appearance.count_products} ({$subcat.product_count}){/if}</a>
</div>
{/foreach}
</div>
</div>
<div class="clear"></div>
<!-- cw@subcategories ] -->

{*/capture} 
{capture name=section_title}{$lng.lbl_more_choices|substitute:category:$current_category.category}{/capture}
{include file='common/section.tpl' is_dialog=1 title=$smarty.capture.section_title content=$smarty.capture.section style='subcategories'*}
{/if}
</section>
