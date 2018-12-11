{if $config.google_analytics.google_analytics_account ne '' && $config.google_analytics.google_analytics_type eq 'asynchronous'}
<script type="text/javascript">
//<![CDATA[[CDATA[
/*pagespeed_no_defer*/
var _gaq = _gaq || [];
_gaq.push(["_setAccount", "{$config.google_analytics.google_analytics_account}"]);
_gaq.push(["_trackPageview"]);

{if 
  $config.google_analytics.google_analytics_e_commerce eq "Y" 
  and $main eq "order_message"
  and $orders
}
  // Ecommerce Tracking for order_message page
  {foreach from=$orders item="order"}
    _gaq.push(["_addTrans",
        "{$order.doc_id}", // order ID - required
        "{$partner|default:'Main stock'}", // affiliation or store name
        "{$order.info.total}", // total - required
        "{if $order.info.tax gt 0}{$order.info.tax}{/if}", // tax
        "{if $order.info.shipping_cost gt 0}{$order.info.shipping_cost}{/if}", // shipping
        "{$order.userinfo.main_address.city|escape:javascript}", // city
        "{$order.userinfo.main_address.state|escape:javascript}", // state or province
        "{$order.userinfo.main_address.countryname|escape:javascript}" // country
    ]);

    {foreach from=$order.products item="product"}
      _gaq.push(["_addItem",
        "{$order.doc_id}",           // order ID - required
        "{$product.productcode|escape:javascript}", // SKU/code - required
        "{$product.product|escape:javascript}{if $active_modules.Product_Options ne "" and $product.product_options_txt} ({$product.product_options_txt|replace:"\n":", "|escape:javascript}){/if}", // product name
        "{$product.category|default:'Unknown category'}", // category or variation
        "{$product.price}",          // unit price - required
        "{$product.amount}"          // quantity - required
      ]);
    {/foreach}

  {/foreach}
  _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
{/if}

(function() {ldelim}
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
{rdelim})();

//]]>
</script>
{/if}
