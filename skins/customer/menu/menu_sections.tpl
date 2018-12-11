{foreach from=$sections item=section_item}
{assign var="section_name" value=$section_item.section}
{* kornev, for the menu id *}
{*
{assign var='id' value=$section_name|id}
{if $section_name eq 'categories'}
    {include file='customer/menu/categories.tpl'}
{elseif $section_name eq 'special_product_links'}
    {include file='customer/menu/special_product_links.tpl'}
{elseif $section_name eq 'manufacturers' && $addons.manufacturers and $config.manufacturers.manufacturers_menu eq 'Y'}
    {include file='addons/manufacturers/menu_manufacturers.tpl'}
{elseif $section_name eq 'recent_categories'}
    {include file='customer/menu/recent_categories.tpl'}
{elseif $section_name eq 'accessories'}
    {include file="customer/menu/accessories.tpl" }
{elseif $section_name eq 'your_cart'}
    {include file='customer/menu/cart.tpl' id='menu_cart'}
{elseif $section_name eq 'authentication'}
    {if $customer_id}
    {include file='elements/authbox.tpl'}
    {else}
    {include file='elements/auth.tpl'}
    {/if}
{elseif $section_name eq 'my_account' && $customer_id}
    {include file='customer/menu/account.tpl' style="border"}
{elseif $section_name eq 'top_sellers' && $addons.bestsellers}
    {include file="addons/bestsellers/menu_bestsellers.tpl" style="border"}
{elseif $section_name eq 'new_arrivals'}
    {include file="customer/menu/arrivals.tpl" }
{elseif $section_name eq 'product_filter' && $config.product.pf_position eq 'menu'}
    {include file="customer/product-filter/menu-view/`$config.product.pf_template`.tpl" }
{elseif $section_name eq 'news'}
    {include file='addons/news/menu/news.tpl'}
    {cms service_code="under_news"}
{elseif $section_name eq 'resources'}
    {include file='customer/menu/resource.tpl'}
{elseif $section_name eq 'online_support'}
    {include file='customer/menu/online_support.tpl'}
{elseif $section_name eq 'dealers_and_distributers' && !$customer_id}
    {include file='customer/menu/warehouse.tpl' style='border'}
{/if}
*}
    {if $section_item.section_template ne ''}
      {include file=$section_item.section_template section_name=$section_name}
    {/if}
    {include file='customer/menu/addon_section.tpl'}
{/foreach}
{cms service_code="special_offer"}
