{if !$data && $func}
    {tunnel func=$func load=$load assign='data'}
{/if}
<select class="form-control" name="{$name}"
        id="{$name|id}"{if $disabled} disabled{/if}{if $onchange} onchange="{$onchange}"{/if}{if $multiple} multiple="{$multiple}"{/if}>
    {if $is_please_select}
        <option value="">{$lng.lbl_please_select}</option>
    {/if}
    {foreach from=$data item=item}
        {assign var='id' value=$item.$field_id}
        {assign var='field_value' value=$item.$field}
        {if is_array($value)}
            <option value="{$id}" {if in_array($id,$value)}selected="selected"{/if}>
        {else}
            <option value="{$id}"{if $id eq $value} selected="selected"{/if}>
        {/if}
        {if $modifier eq 'user_title'}{$field_value|user_title}{else}{$field_value|default:$default}{/if}</option>
    {/foreach}
</select>
