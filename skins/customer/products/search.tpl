<div class="search_page {if $products}products_found{/if}">
{capture name=dialog}

{if $mode ne 'search' or (!$products && !$search_prefilled.attributes)}

<form name="search_form" id="search_form" action="index.php?target=search" method="post">
<input type="hidden" name="action" value="search" />
{include file='customer/products/search_form.tpl' prefix='posted_data' form_name='search_form'}
</form>

{/if}

{if !$facet_category.image.is_default}<div class="image">{include file='common/thumbnail.tpl' image=$facet_category.image}</div>{/if}


{* Build description from selected attributes descriptions *}
<div id='filter_description' class='filter_description'>
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
</div>

{if $replaced_url}
	<input id="replaced_url" type="hidden" value="{$replaced_url}" />
{/if}

<a name="results"></a>
{/capture}
{include file='common/section.tpl' is_dialog=1 title=$lng.lbl_search_products content=$smarty.capture.dialog additional_class='asearch'}

{if $mode eq 'search'}
{if $products}
<!-- cw@products_top [ -->
{include file='customer/products/products_top.tpl'}
<!-- cw@products_top ] -->

{/if}
<div class="tab_general_content">
{include file="customer/products/`$product_list_template`.tpl" products=$products}
{if $navigation.total_pages gt 2}<div class="nav_bottom">{include file='common/navigation_customer.tpl'}</div>{/if}
</div>

{/if}
</div>
