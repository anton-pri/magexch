{if !$classes}{tunnel func='cw_product_classes_list' load='product_classes' assign='classes'}{/if}
<select name="{$name}" id="{$name|id}" {if $multiple} multiple="multiple" size="{$multiple}"{/if}{if $read_only} disabled{/if}{if $onchange} onchange="javascript: {$onchange}"{/if}>
{if $is_please_select}
    <option value="0"{if $selected.0 || 0 eq $value} selected="selected"{/if}>{$lng.lbl_please_select}</option>
{/if}
{foreach from=$classes item=class}
{assign var="id" value=$class.fclass_id}
    <option value="{$id}"{if $selected.$id || $id eq $value} selected="selected"{/if}>{$class.class}</option>
{/foreach}
</select>
