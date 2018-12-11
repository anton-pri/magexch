{tunnel func='cw_attributes_get_all_for_products' via='cw_call' assign='attributes' param1=$is_show}
<select name="{$name}"{if $multiple} multiple size="{$multiple}"{/if} id="{$id|default:"`$name|id`"}"{if $onchange} onchange="{$onchange}"{/if}>
{if $is_please_select}
<option value="">{$lng.lbl_please_select}</option>
{/if}
{foreach from=$attributes item=tp}
<option value="{$tp.attribute_id}"{if (!$multiple && $value eq $tp.attribute_id) || ($multiple && count($value) && in_array($tp.attribute_id, $value))} selected{/if}>{$tp.name}</option>
{/foreach}
</select>
