{if $product_filter || $search_prefilled.attributes.substring}

{capture name=menu}
{* use the same template - but with different styles *}
    {include file='customer/product-filter/general-view/default.tpl'}
{/capture}
{include file='common/section.tpl' title='' content=$smarty.capture.menu style='product-filter'}
{/if}
