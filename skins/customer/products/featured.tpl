{if $featured_products}
{capture name=dialog}
{include file='customer/products/products_gallery.tpl' products=$featured_products featured='Y'}
{/capture}
{include file='common/section.tpl' is_dialog=1  content=$smarty.capture.dialog title=$lng.lbl_featured_products}
{/if}

