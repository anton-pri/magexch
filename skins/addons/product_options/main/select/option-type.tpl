{if $is_text}
    {if $value eq 'Y'}{$lng.lbl_modificator}{/if}
    {if $value eq ''}{$lng.lbl_variant}{/if}
    {if $value eq 'T'}{$lng.lbl_text_field}{/if}
{else}
<select class="form-control" name="{$name}"{if $onchange} onchange="{$onchange}"{/if} {if $id}id='{$id}'{/if}>
    <option value='Y'{if $value eq 'Y'} selected="selected"{/if}>{$lng.lbl_modificator}</option>
    <option value=''{if $value eq ''} selected="selected"{/if}>{$lng.lbl_variant}</option>
    <option value='T'{if $value eq 'T'} selected="selected"{/if}>{$lng.lbl_text_field}</option>
</select>
{/if}
