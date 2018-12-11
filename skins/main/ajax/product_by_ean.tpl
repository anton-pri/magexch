{strip}
{ldelim}
"el_product":"{$el_product|escape:"json"}",
"product":"{capture name=product}{if $product.product_id}{$product.product} ({$product.avail}/{$product.avail_backorder}) {include file='main/visiblebox_link.tpl' mark="open_close_product`$time`" do_not_include=true}<br/><div id="open_close_product{$time}" style="display:none;">{include file='main/products/product/avails.tpl'}</div>{else}{$lng.lbl_not_found}{/if}{/capture}{$smarty.capture.product|escape:"json"}"
{rdelim}
{/strip}
