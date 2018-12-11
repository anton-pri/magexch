{if $text_only}
    {tunnel func='cw_manufacturer_get_smarty' manufacturer_id=$value assign='manufacturer'}
    {$manufacturer.manufacturer}
{else}
    {if !$manufacturers}{tunnel func='cw_manufacturer_get_list_smarty' assign='manufacturers'}{/if}
<select class="form-control" name="{$name}" id="{$name|id}"{if $multiple} multiple="multiple" size="{$multiple}"{/if}{if $read_only} disabled{/if}{if $onchange} onchange="javascript: {$onchange}"{/if}>
    {if $is_please_select}
    <option value="0"{if $value.0 || $value eq 0} selected="selected"{/if}>{$lng.lbl_please_select}</option>
    {/if}
    {foreach from=$manufacturers item=manufacturer}
    {assign var='id' value=$manufacturer.manufacturer_id}
    <option value="{$id}"{if ($multiple && $value.$id) || ($multiple && is_array($value) && in_array($id, $value)) || (!$multiple && $id eq $value)} selected="selected"{/if}>{$manufacturer.manufacturer}</option>
    {/foreach}
</select>
{/if}
