{tunnel func='cw_barcode_get_templates' load='barcode' assign='templates'}
<select name="{$name}" id="{$name|id}" {if $disabled} disabled{/if}{if $onchange} onchange="javascript: {$onchange}"{/if} >
<option value="">{$lng.lbl_please_select}</option>
{if $templates}
{foreach from=$templates item=template}
{assign var="key" value=$template.layout_id}
<option value="{$key}"{if $key eq $value} selected="selected"{/if}>{$template.title}</option>
{/foreach}
{/if}
</select>
