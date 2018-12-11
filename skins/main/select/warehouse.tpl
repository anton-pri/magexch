{tunnel func='cw_warehouse_get_divisions' load='warehouse' assign='warehouses'}
<select name="{$name}"{if $disabled} disabled{/if}{if $onchange} onchange="{$onchange}"{/if}{if $class} class="{$class}"{/if}{if $multiple} multiple size="5"{/if}>
{if !$multiple}
<option value="">{$lng.lbl_please_select}</option>
{/if}
{foreach from=$warehouses item=warehouse}
{assign var='id' value=$warehouse.division_id}
<option value="{$id}"{if (!$multiple && $id eq $value) || ($multiple && $value.$id)} selected="selected"{/if}>{$warehouse.title}</option>
{/foreach}
</select>
