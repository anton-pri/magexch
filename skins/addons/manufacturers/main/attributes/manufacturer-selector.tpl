{if $attribute.field eq 'manufacturer_id'}
{tunnel func='cw_manufacturer_get_list_smarty' assign='manufacturers'}
    <select name="attributes[{$attribute.field}]" class="manufacturer_select">
        <option value="0"{if $attribute.value eq 0} selected{/if}></option>
        {foreach from=$manufacturers item=manufacturer}
        <option value="{$manufacturer.manufacturer_id}"{if $manufacturer.manufacturer_id eq $attribute.value} selected{/if}>{$manufacturer.manufacturer}</option>
        {/foreach}
    </select>
{/if}
