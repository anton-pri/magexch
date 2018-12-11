{tunnel func='cw_attributes_get_all_classes_for_products' assign='attributes_classes'}
<select name="{$name}"{if $multiple} multiple size="{$multiple}"{/if} id="{$id|default:"`$name|id`"}"{if $onchange} onchange="{$onchange}"{/if}>
{if $is_please_select}
<option value="">{$lng.lbl_please_select}</option>
{/if}
{foreach from=$attributes_classes item=tp}
<option value="{$tp.attribute_class_id}"{if $value eq $tp.attribute_class_id || (!$value && $tp.is_default)} selected{/if}>{$tp.name}</option>
{/foreach}
</select>
