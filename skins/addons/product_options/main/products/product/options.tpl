{if ($submode eq 'product_options_add' || $product_options eq '' || $product_option ne '')}
    {include file='addons/product_options/main/products/product/option.tpl'}
{else}
    {include file='addons/product_options/main/products/product/option_list.tpl'}
{/if}
