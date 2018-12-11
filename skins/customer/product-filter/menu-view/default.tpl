{if $product_filter || $search_prefilled.attributes.substring}

{*capture name=menu*}
{* use the same template - but with different styles *}
    {include file='customer/product-filter/general-view/default.tpl'}
{*/capture}
{include file='common/menu.tpl' title=$lng.lbl_product_filter content=$smarty.capture.menu style='product-filter' *}
{/if}
