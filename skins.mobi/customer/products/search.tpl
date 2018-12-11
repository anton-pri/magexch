
<div class="search_page">
{capture name=dialog}

{if $mode ne 'search' or (!$products && !$search_prefilled.attributes)}

<form name="search_form" action="index.php?target=search" method="post">
<input type="hidden" name="action" value="search" />
{include file='customer/products/search_form.tpl' prefix='posted_data' form_name='search_form'}
</form>


{/if}

<a name="results"></a>
{/capture}
{include file='common/section.tpl' is_dialog=1 title=$lng.lbl_search_products content=$smarty.capture.dialog additional_class='asearch'}

{if $mode eq 'search'}
{*include file='common/navigation_counter.tpl'*}

{include file='customer/products/products_top.tpl'}
<div class="tab_general_content">
{*include file='common/navigation.tpl'*}
{if $set_view eq 0}{assign var=product_list_template value=products_gallery}{/if}

{include file="customer/products/`$product_list_template`.tpl" products=$products}
{if $navigation.total_pages gt 2}<div class="nav_bottom">{include file='common/navigation_customer.tpl'}</div>{/if}
</div>

{/if}
</div>
