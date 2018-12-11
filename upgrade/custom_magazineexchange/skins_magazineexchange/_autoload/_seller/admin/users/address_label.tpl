{* This is address label. It is just structured document for using everywhere to show address, pass parameter $class and use CSS for appearance in every very case *}
<div id='address_{$address.address_id}' class='address_label {$class}'>
{if $profile_fields.address.firstname.is_avail || $profile_fields.address.lastname.is_avail}
<div class="address_label_row address_label_name">
<div class="add-l">{$lng.lbl_name}:</div>
<div class="add-r">
    {if $profile_fields.address.firstname.is_avail}{$address.firstname}{/if}
    {if $profile_fields.address.lastname.is_avail}{$address.lastname}{/if}
</div>
</div>
{/if}
{if $profile_fields.address.address.is_avail && $address.address}
<div class="address_label_row address_label_address">
<div class="add-l">{$lng.lbl_address}:</div>
<div class="add-r">{$address.address}</div>
</div>
{/if}
{if $profile_fields.address.address_2.is_avail && $address.address_2}
<div class="address_label_row address_label_address2">
<div class="add-l">&nbsp;</div>
<div class="add-r">{$address.address_2}</div>
</div>
{/if}
<!-- {if $profile_fields.address.city.is_avail}
<div class="address_label_row address_label_city">
 <strong>{$lng.lbl_city}:</strong>  
<div class="add-l">&nbsp;</div>
<div class="add-r">{$address.city}</div>
</div>
{/if}-->
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
<div class="add-l">{$lng.lbl_zipcode}:</div>
<div class="add-r">{$address.zipcode}</div>
</div>
{/if}
{if $profile_fields.address.country.is_avail}
<div class="address_label_row address_label_country">
<div class="add-l">{$lng.lbl_country}:</div>
<div class="add-r">{$address.countryname}</div>
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
