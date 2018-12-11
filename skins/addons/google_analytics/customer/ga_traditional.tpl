{if $config.google_analytics.google_analytics_account ne '' && $config.google_analytics.google_analytics_type eq 'traditional'}
<script type="text/javascript">
//<![CDATA[[CDATA[
/*pagespeed_no_defer*/
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
//]]>
</script>

<script type="text/javascript">
//<![CDATA[[CDATA[
/*pagespeed_no_defer*/
try{ldelim}
var pageTracker = _gat._getTracker("{$config.google_analytics.google_analytics_account}");
pageTracker._trackPageview();
{rdelim} catch(err) {ldelim}{rdelim}
//]]>
</script>

{if 
  $config.google_analytics.google_analytics_e_commerce eq "Y"
  and $main eq "order_message"
  and $orders
}
<script type="text/javascript">
//<![CDATA[[CDATA[
/*pagespeed_no_defer*/
if (pageTracker && pageTracker._addTrans && pageTracker._trackTrans) {ldelim}
{foreach from=$orders item="order"}
pageTracker._addTrans(
"{$order.doc_id}", // order ID - required
"{$partner|default:'Main stock'}", // affiliation or store name
"{$order.info.total}", // total - required
"{if $order.info.tax gt 0}{$order.info.tax}{/if}", // tax
"{if $order.info.shipping_cost gt 0}{$order.info.shipping_cost}{/if}", // shipping
"{$order.userinfo.main_address.city|escape:javascript}", // city
"{$order.userinfo.main_address.state|escape:javascript}", // state or province
"{$order.userinfo.main_address.countryname|escape:javascript}" // country
);
{foreach from=$order.products item="product"}
pageTracker._addItem(
"{$order.doc_id}", // order ID - required
"{$product.productcode|escape:javascript}", // SKU/code
"{$product.product|escape:javascript}{if $active_modules.Product_Options ne "" and $product.product_options_txt} ({$product.product_options_txt|replace:"\n":", "|escape:javascript}){/if}", // product name
"{$product.category|default:'Unknown category'}", // category or variation
"{$product.price}", // unit price - required
"{$product.amount}" // quantity - required
);
{/foreach}
{/foreach}
pageTracker._trackTrans();
{rdelim}
//]]>
</script>
{/if}
{/if}
