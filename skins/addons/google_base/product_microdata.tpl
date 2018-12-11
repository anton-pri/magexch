{tunnel func='cw_gb_product_microdata' via='cw_call' param1=$product.product_id assign='microdata'}
<!-- this part must be inside itemscope itemtype="http://schema.org/Product" -->
<meta itemprop="sku" content="{$product.productcode}" />
<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
  <meta itemprop="priceCurrency" content="{$config.google_base.gb_currency|default:'USD'}" />
  <meta itemprop="price" content="{$product.taxed_price}" />
  <link itemprop="itemCondition" itemtype="http://schema.org/OfferItemCondition" content="{if $microdata['g:condition'] eq 'new' || $microdata['g:condition'] eq ''}http://schema.org/NewCondition{/if}" />
  <link itemprop="availability" href="{if $product.distribution}http://schema.org/OnlineOnly{elseif $product.avail}http://schema.org/InStock{else}http://schema.org/OutOfStock{/if}" />
</div>

