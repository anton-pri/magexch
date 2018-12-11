<div class="category_info">

{if !$manufacturer.image.is_default}
<div class="image">
    {if $manufacturer.url ne ''}<a href="{$manufacturer.url}">{/if}
    {include file='common/thumbnail.tpl' image=$manufacturer.image}
    {if $manufacturer.url ne ''}</a>{/if}
</div>
{/if}
<h1 class="title">{$manufacturer.manufacturer}</h1>
<div class="descr">{$manufacturer.descr}</div>
</div>

{if $products ne ''}
{include file='customer/products/products_top.tpl'}
<div class="tab_general_content">
{include file="customer/products/`$product_list_template`.tpl" products=$products}
{if $navigation.total_pages gt 2}<div class="nav_bottom">{include file='common/navigation_customer.tpl'}</div>{/if}
</div>
{else}
{$lng.txt_no_products_in_man}
{/if}