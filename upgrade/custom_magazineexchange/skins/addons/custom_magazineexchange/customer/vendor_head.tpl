{if $vendorid}
  {if $shopfront eq ''}
    {tunnel func='cw\custom_magazineexchange_sellers\mag_get_shopfront' via='cw_call' param1=$vendorid assign='shopfront'}
  {/if}
   <div class="clearing"></div>
   <div style="padding: 0 20px 16px 20px"><h1 style="font-size:20px; font-weight: normal">{$shopfront.shop_name}</h1></div>
   <div class="vendor_head_short_desc"><h1 style="font-size:12px; font-weight: normal">{$shopfront.short_desc}</h1></div>
   <div style="float:right; width:200px;">
{if !$shopfront.image.is_default}
<div class="image">
    {include file='common/thumbnail.tpl' image=$shopfront.image}
</div>
{/if}
</div>
{/if}
