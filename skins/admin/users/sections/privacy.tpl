{if $profile_fields.privacy.privacy_given_date.is_avail}
<div class="input_field_{$profile_fields.privacy.privacy_given_date.is_required}">
    <label>{$lng.lbl_privacy_given_date}</label>
    {include file="main/select/date.tpl" name="privacy[privacy_given_date]" value=$userinfo.customer_info.privacy_given_date}
    {if $fill_error.privacy.privacy_given_date}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{if $profile_fields.privacy.privacy_signature_date.is_avail}
<div class="input_field_{$profile_fields.privacy.privacy_signature_date.is_required}">
    <label>{$lng.lbl_privacy_signature_date}</label>
    {include file="main/select/date.tpl" name="customer_info[privacy_signature_date]" value=$userinfo.customer_info.privacy_signature_date}
    {if $fill_error.privacy.privacy_signature_date}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{include file='main/users/sections/custom.tpl'}
