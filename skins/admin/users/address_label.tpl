{* This is address label. It is just structured document for using everywhere to show address, pass parameter $class and use CSS for appearance in every very case *}
<div id='address_{$address.address_id}' class='address_label {$class}'>
{if $profile_fields.address.firstname.is_avail || $profile_fields.address.lastname.is_avail}
<div class="address_label_row address_label_name">
<strong>{$lng.lbl_name}:</strong>
<span>
    {if $profile_fields.address.firstname.is_avail}{$address.firstname}{/if}
    {if $profile_fields.address.lastname.is_avail}{$address.lastname}{/if}
</span>
</div>
{/if}
{if $profile_fields.address.address.is_avail && $address.address}
<div class="address_label_row address_label_address">
<strong>{$lng.lbl_address}:</strong>
<span>
{$address.address}
</span>
</div>
{/if}
{if $profile_fields.address.address_2.is_avail && $address.address_2}
<div class="address_label_row address_label_address2">
<span>
{$address.address_2}
</span>
</div>
{/if}
{if $profile_fields.address.city.is_avail}
<div class="address_label_row address_label_city">
<strong>{$lng.lbl_city}:</strong>
<span>
{$address.city}
</span>
</div>
{/if}
{if $profile_fields.address.state.is_avail && $address.statename}
<div class="address_label_row address_label_state">
<strong>{$lng.lbl_state}:</strong>
<span>
{$address.statename}
</span>
</div>
{/if}
{if $profile_fields.address.zipcode.is_avail}
<div class="address_label_row address_label_zipcode">
<strong>{$lng.lbl_zipcode}:</strong>
<span>
{$address.zipcode}
</span>
</div>
{/if}
{if $profile_fields.address.country.is_avail}
<div class="address_label_row address_label_country">
<strong>{$lng.lbl_country}:</strong>
<span>
{$address.countryname}
</span>
</div>
{/if}
{if $profile_fields.address.phone.is_avail}
<div class="address_label_row address_label_phone">
<strong>{$lng.lbl_phone}:</strong>
<span>
{$address.phone}
</span>
</div>
{/if}

{foreach from=$profile_fields.address key=field_id item=field}
{if $field.is_avail && $field.type neq 'D'}
<div class="address_label_row address_label_{$field.field}">
<strong>{$field.title}:</strong>
<span>
{$address.custom_fields[$field_id]}
</span>
</div>
{/if}
{/foreach}

</div>
