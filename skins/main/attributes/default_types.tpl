{if $attribute.type eq 'text' || $attribute.type eq 'integer' || $attribute.type eq 'decimal'}
<input type="text" class='{if $attribute.is_required}required{/if} short' name="{$fieldname}[{$attribute.field}]" value="{$attribute.value|escape}">

{elseif $attribute.type eq 'textarea'}
<textarea class='{if $attribute.is_required}required{/if}' name="{$fieldname}[{$attribute.field}]" cols="80" rows="6">{$attribute.value}</textarea>

{elseif $attribute.type eq 'yes_no'}
{include file='main/select/yes_no.tpl' name="`$fieldname`[`$attribute.field`]" value=$attribute.value}

{elseif $attribute.type eq 'date'}
{html_select_date field_array="`$fieldname`[`$attribute.field`]" start_year="-10" end_year="+10"  time=$attribute.value}

{elseif $attribute.type eq 'multiple_selectbox' || $attribute.type eq 'selectbox'}
<select class='{if $attribute.is_required}required{/if}' name="{$fieldname}[{$attribute.field}]{if $attribute.type eq 'multiple_selectbox'}[]{/if}" {if $attribute.type eq 'multiple_selectbox'} multiple{/if}>
    {if $attribute.type eq 'selectbox' and !$attribute.is_required}
    <option value=""{if in_array('', $attribute.values)} selected{/if}>{$lng.lbl_please_select}</option>
    {/if}
    {foreach from=$attribute.default_value key=key item=option}
    <option value="{$option.attribute_value_id|escape}"{if in_array($option.attribute_value_id, $attribute.values)} selected{/if}>{$option.value}</option>
    {/foreach}
</select>
{elseif $attribute.type eq 'rating' && $rating_show}
<input type="text" class='{if $attribute.is_required}required{/if}' name="{$fieldname}[{$attribute.field}]" value="{$attribute.value|escape}">

{/if}

