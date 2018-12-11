{tunnel func='cw_product_get_types' assign='product_types'}
<select name="{$name}"{if $multiple} multiple="multiple" size="3"{/if}>
{foreach from=$product_types item=type}
{assign var="key" value=$type.product_type_id}
    <option value="{$key}"{if $selected.$key || $key eq $value} selected="selected"{/if}>{$type.title}</option>
{/foreach}
</select>
