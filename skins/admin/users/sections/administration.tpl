{if $profile_fields.administration.special_tax.is_avail}
<div class="input_field_{$profile_fields.administration.special_tax.is_required}">
    <label>{$lng.lbl_special_tax}</label>
    {include file="main/users/manage_special_tax.tpl" name="update_fields[administration][special_tax]" value=$userinfo.additional_info.tax_id}
    {if $fill_error.administration.special_tax}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{if $profile_fields.administration.payment_id.is_avail}
<div class="input_field_{$profile_fields.administration.payment_id.is_required}">
    <label>{$lng.lbl_payment_id}</label>
    {include file="main/select/payment.tpl" name="update_fields[administration][payment_id]" value=$userinfo.additional_info.payment_id payments=$payment_methods is_please_select=1}
    {if $fill_error.administration.payment_id}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{if $profile_fields.administration.payment_note.is_avail}
<div class="input_field_{$profile_fields.administration.payment_note.is_required}">
    <label>{$lng.lbl_payment_note}</label>
    <textarea name="update_fields[administration][payment_note]">{$userinfo.additional_info.payment_note}</textarea>
    {if $fill_error.administration.payment_note}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{if $profile_fields.administration.separate_invoices.is_avail}
<div class="input_field_{$profile_fields.administration.separate_invoices.is_required}">
    <label>{$lng.lbl_separate_invoices}</label>
    <input type="hidden" name="update_fields[administration][separate_invoices]" value="0">
    <input type="checkbox" name="update_fields[administration][separate_invoices]" value="1" {if $userinfo.additional_info.separate_invoices}checked{/if} />
    {if $fill_error.administration.separate_invoices}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
{include file='main/users/sections/custom.tpl'}
