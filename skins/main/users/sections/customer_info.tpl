{if $profile_fields.customer_info.birthday.is_avail}
<div class="input_field_{$profile_fields.customer_info.birthday.is_required}">
    <label>{$lng.lbl_birthday}</label>
    {include file="main/select/date.tpl" name="update_fields[customer_info][birthday]" value=$userinfo.additional_info.birthday}
    {if $fill_error.customer_info.birthday}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{if $profile_fields.customer_info.birthday_place.is_avail}
<div class="input_field_{$profile_fields.customer_info.birthday_place.is_required}">
    <label>{$lng.lbl_birthday_place}</label>
    <input type="text" name="update_fields[customer_info][birthday_place]" value="{$userinfo.additional_info.birthday_place}" />
    {if $fill_error.customer_info.birthday_place}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{if $profile_fields.customer_info.sex.is_avail}
<div class="input_field_{$profile_fields.customer_info.sex.is_required}">
    <label>{$lng.lbl_sex}</label>
    {include file="main/select_sex.tpl" name="update_fields[customer_info][sex]"  value=$userinfo.additional_info.sex}
    {if $fill_error.customer_info.sex}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{if $profile_fields.customer_info.married.is_avail}
<div class="input_field_{$profile_fields.customer_info.married.is_required}">
    <label>{$lng.lbl_married}</label>
    {include file="main/select_married.tpl" name="update_fields[customer_info][married]" value=$userinfo.additional_info.married}
    {if $fill_error.customer_info.married}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{if $profile_fields.customer_info.nationality.is_avail}
<div class="input_field_{$profile_fields.customer_info.nationality.is_required}">
    <label>{$lng.lbl_nationality}</label>
    <input type="text" name="update_fields[customer_info][nationality]" value="{$userinfo.additional_info.nationality}" />
    {if $fill_error.customer_info.nationality}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{include file='main/users/sections/custom.tpl'}
