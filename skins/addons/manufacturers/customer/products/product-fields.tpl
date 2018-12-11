{if $attributes.manufacturer_id.value}
    {tunnel func='cw_manufacturer_get' manufacturer_id=$attributes.manufacturer_id.value assign='manufacturer'}
    {if $manufacturer.manufacturer_id}
    <li class="product_field{cycle values=", cycle"}">
        <label class="field-title">{$lng.lbl_manufacturer}:</label>
        <div>
        {$manufacturer.manufacturer}<br /> 
        <a href="{pages_url var='manufacturers' manufacturer_id=$manufacturer.manufacturer_id}">{$lng.lbl_view_all_prod_by} {$manufacturer.manufacturer}</a>

        {if $attributes.manufacturer_web.value}
        <a href="{$attributes.manufacturer_web.value}">{$lng.lbl_manufacturer_web_product}</a>
        {elseif $manufacturer.url}
        <a href="{$manufacturer.url}">{$lng.lbl_manufacturer_web_product}</a>
        {/if}
        </div>
    </li>
    {/if}
{/if}
