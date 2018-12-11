{if $profile_fields.contact_list.reference.is_avail}
<div class="input_field_{$profile_fields.contact_list.reference.is_required}">
    <label>{$lng.lbl_reference}</label>
    <input type="text" name="contact_list[reference]" size="32" maxlength="32" value="{$contact_list.reference}"{if $readonly} disabled{/if} />
    {if $fill_error.reference}<font class="Star">&lt;&lt;</font>{/if}
</div>
{/if}

{if $profile_fields.contact_list.phone.is_avail}
<div class="input_field_{$profile_fields.contact_list.phone.is_required}">
    <label>{$lng.lbl_phone}</label>
    <input type="text" name="contact_list[phone]" size="32" maxlength="32" value="{$contact_list.phone}"{if $readonly} disabled{/if} />
    {if $fill_error.phone}<font class="Star">&lt;&lt;</font>{/if}
</div>
{/if}

{if $profile_fields.contact_list.cell_phone.is_avail}
<div class="input_field_{$profile_fields.contact_list.cell_phone.is_required}">
    <label>{$lng.lbl_cell_phone}</label>
    <input type="text" name="contact_list[cell_phone]" size="32" maxlength="64" value="{$contact_list.cell_phone}"{if $readonly} disabled{/if} />
    {if $fill_error.cell_phone}<font class="Star">&lt;&lt;</font>{/if}
</div>
{/if}

{if $profile_fields.contact_list.fax.is_avail}
<div class="input_field_{$profile_fields.contact_list.fax.is_required}">
    <label>{$lng.lbl_fax}</label>
    <input type="text" name="contact_list[fax]" size="32" maxlength="64" value="{$contact_list.fax}"{if $readonly} disabled{/if} />
    {if $fill_error.fax}<font class="Star">&lt;&lt;</font>{/if}
</div>
{/if}

{if $profile_fields.contact_list.email.is_avail}
<div class="input_field_{$profile_fields.contact_list.email.is_required}">
    <label>{$lng.lbl_email}</label>
    <input type="text" name="contact_list[email]" size="32" maxlength="64" value="{$contact_list.email}"{if $readonly} disabled{/if} />
    {if $fill_error.email}<font class="Star">&lt;&lt;</font>{/if}
</div>
{/if}

{if $profile_fields.contact_list.position.is_avail}
<div class="input_field_{$profile_fields.contact_list.position.is_required}">
    <label>{$lng.lbl_position}</label>
    <input type="text" name="contact_list[position]" size="32" maxlength="32" value="{$contact_list.position}"{if $readonly} disabled{/if} />
    {if $fill_error.position}<font class="Star">&lt;&lt;</font>{/if}
</div>
{/if}

{if $profile_fields.contact_list.note.is_avail}
<div class="input_field_{$profile_fields.contact_list.note.is_required}">
    <label>{$lng.lbl_note}</label>
    <textarea cols="40" rows="4" name="contact_list[note]"{if $readonly} disabled{/if}>{$contact_list.note}</textarea>
    {if $fill_error.note}<font class="Star">&lt;&lt;</font>{/if}
</div>
{/if}

{include file='main/users/sections/custom.tpl' included_tab='contact_list' fv=$contact_list.custom_fields name_prefix='contact_list[custom_fields]'}
