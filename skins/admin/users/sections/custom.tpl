{assign var='fields' value=$profile_fields.$included_tab}
{assign var='errors' value=$fill_error.$included_tab}
{if !$fv}{assign var='fv' value=$userinfo.custom_fields}{/if}
{if !$name_prefix}{assign var='name_prefix' value="update_fields[`$included_tab`]"}{/if}
{foreach from=$fields item=field}
{if $field.type ne 'D'}
{assign var='field_id' value=$field.field_id}
{assign var='value' value=$fv.$field_id}
<div class="form-group input_field_{$field.is_required}">
    <label {if $field.is_required}class='required'{/if}>{$field.title}</label>
    {if $field.type eq 'T'}
<input type="text" class='form-control {if $field.is_required}required{/if}' name="{$name_prefix}[{$field_id}]" id="{$name_prefix|id}_{$field_id|id}" value="{$value|escape}" />
    {elseif $field.type eq 'C'}
<input type="checkbox" name="{$name_prefix}[{$field_id}]" id="{$name_prefix|id}_{$field_id|id}" value="Y"{if $value eq 'Y'} checked="checked"{/if}{if $readonly} disabled{/if} />
    {elseif $field.type eq 'M'}
        {assign var="values" value=$value}
        {foreach from=$field.variants item=o}
<input type="checkbox" name="{$name_prefix}[{$field_id}][]" id="{$name_prefix|id}_{$field_id|id}" value="{$o|escape}" {if $value.$o} checked{/if}{if $readonly} disabled{/if}>{$o|escape}<br/>
        {/foreach}
    {elseif $field.type eq 'S'}
<select name="{$name_prefix}[{$field_id}]" class='form-control {if $field.is_required}required{/if}' id="{$name_prefix|id}_{$field_id|id}"{if $readonly} disabled{/if}>
{foreach from=$field.variants item=o}
<option value='{$o|escape}'{if $value eq $o} selected="selected"{/if}>{$o|escape}</option>
{/foreach}
</select>
    {/if}
    {if $errors.$field_id}<span class="field_error">&lt;&lt;</span>{/if}
</div>
{/if}
{/foreach}
