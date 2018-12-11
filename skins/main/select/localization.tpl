{tunnel func='cw_localization_get_list' load='localization' assign='localizations'}
<select name="{$name}"{if $onchange} onchange="{$onchange}"{/if}{if $disabled} disabled{/if}>
{foreach from=$localizations item=localization}
<option value="{$localization.localization_id}"{if $localization.localization_id eq $value} selected="selected"{/if}>{$localization.title}</option>
{/foreach}
</select>
