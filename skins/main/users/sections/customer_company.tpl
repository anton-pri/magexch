{if $profile_fields.customer_company.employees.is_avail}
<div class="input_field_{$profile_fields.customer_company.employees.is_required}">
    <label>{$lng.lbl_employees}</label>
    <input type="text" name="update_fields[customer_company][employees]" size="32" maxlength="32" value="{$userinfo.additional_info.employees}" />
    {if $fill_error.customer_company.employees}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{if $profile_fields.customer_company.foundation.is_avail}
<div class="input_field_{$profile_fields.customer_company.foundation.is_required}">
    <label>{$lng.lbl_foundation}</label>
    <input type="text" name="update_fields[customer_company][foundation]" size="32" maxlength="32" value="{$userinfo.additional_info.foundation}" />
    {if $fill_error.customer_company.foundation}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{if $profile_fields.customer_company.foundation_place.is_avail}
<div class="input_field_{$profile_fields.customer_company.foundation_place.is_required}">
    <label>{$lng.lbl_foundation_place}</label>
    <input type="text" name="update_fields[customer_company][foundation_place]" size="32" maxlength="32" value="{$userinfo.additional_info.foundation_place}" />
    {if $fill_error.customer_company.foundation_place}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{include file='main/users/sections/custom.tpl'}
