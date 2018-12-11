{if $main eq "manufacturer_products"}

{if !$manufacturer.image.is_default}
<div class="brand-logo">
    {if $manufacturer.url ne ''}<a href="{$manufacturer.url}">{/if}
    {include file='common/thumbnail.tpl' image=$manufacturer.image}
    {if $manufacturer.url ne ''}</a>{/if}
</div>
{/if}
<div class="block-brand-info">
  <h3 class="title">{$lng.lbl_about}</h3>
  <div class="brand-description">{$manufacturer.descr}</div>
</div>

{/if}