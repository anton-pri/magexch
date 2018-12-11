{if $doc.info.tracking}
<label>Tracking number: </label>
{tunnel func='cw_shipping_doc_trackable' via='cw_call' param1=$doc assign='cw_shipping_doc_trackable'}
{if $cw_shipping_doc_trackable}
 <a href='{$catalogs.customer}/index.php?target=order_tracking&doc_id={$doc.doc_id}&tracking={$doc.info.tracking}' title='Track it'>{$doc.info.tracking}</a>

{* TrackAction *}
{if $doc.status eq 'C'}
<div itemscope itemtype="http://schema.org/ParcelDelivery">
  <div itemprop="deliveryAddress" itemscope itemtype="http://schema.org/PostalAddress">
    <meta itemprop="streetAddress" content="{$doc.userinfo.current_address.address} {$doc.userinfo.current_address.address_2}" />
    <meta itemprop="addressLocality" content="{$doc.userinfo.current_address.city}" />
    <meta itemprop="addressRegion" content="{$doc.userinfo.current_address.state}" />
    <meta itemprop="addressCountry" content="{$doc.userinfo.current_address.country}" />
    <meta itemprop="postalCode" content="{$doc.userinfo.current_address.zipcode}" />
  </div>
  {math assign='arr_until' equation='x+(60*60*24*14)' x=$doc.date} {* Arrive in 14 days *}
  <meta itemprop="expectedArrivalUntil" content="{$doc.info.ship_time|date_format:'%Y-%m-%dT00:00':$arr_until}"/>
  <div itemprop="carrier" itemscope itemtype="http://schema.org/Organization">
    <meta itemprop="name" content="{$doc.info.carrier.carrier}" />
  </div>
{foreach from=$doc.products item=p}
  <div itemprop="itemShipped" itemscope itemtype="http://schema.org/Product">
    <meta itemprop="name" content="{$p.product}"/>
    <meta itemprop="sku" content="{$p.productcode}"/>
  </div>
{/foreach}
  <div itemprop="partOfOrder" itemscope itemtype="http://schema.org/Order">
    <meta itemprop="orderNumber" content="{$doc.display_id}" />
    <meta itemprop="orderStatus" content="OrderInTransit" />    
    <div itemprop="merchant" itemscope itemtype="http://schema.org/Organization">
      <meta itemprop="name" content="SaratogaWine.com" />
    </div>
  </div>
  <link itemprop="trackingUrl" href="{$current_location}/index.php?target=order_tracking&doc_id={$doc.doc_id}&tracking={$doc.info.tracking}" />
  <div itemprop="potentialAction" itemscope itemtype="http://schema.org/TrackAction">
    <link itemprop="target" href="{$current_location}/index.php?target=order_tracking&doc_id={$doc.doc_id}&tracking={$doc.info.tracking}" />
    <meta itemprop="url" content="{$current_location}/index.php?target=order_tracking&doc_id={$doc.doc_id}&tracking={$doc.info.tracking}" />
  </div>
  <meta itemprop="trackingUrl" content="{$current_location}/index.php?target=order_tracking&doc_id={$doc.doc_id}&tracking={$doc.info.tracking}" />
  <meta itemprop="trackingNumber" content="{$doc.info.tracking}" />
</div>
{/if}
{* / TrackAction *} 

{else}
 {$doc.info.tracking}
{/if}
{/if}

{* ViewAction *}
<div itemscope itemtype="http://schema.org/EmailMessage">
  <div itemprop="potentialAction" itemscope itemtype="http://schema.org/ViewAction">
    <link itemprop="target" href="{$current_location}/index.php?target=docs_O&mode=details&doc_id={$doc.doc_id}" />
    <meta itemprop="name" content="View" />
    <meta itemprop="url" content="{$current_location}/index.php?target=docs_O&mode=details&doc_id={$doc.doc_id}"/>
  </div>
  <meta itemprop="description" content="Order details" />
</div>
{* / ViewAction *}
