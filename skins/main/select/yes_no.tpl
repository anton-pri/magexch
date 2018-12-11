{if $is_text}
    {if $value}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}
{else}
<select name="{$name}" style="min-width: 50px;">
    <option value='1'{if $value} selected="selected"{/if}>{$lng.lbl_yes}</option>
    <option value='0'{if !$value} selected="selected"{/if}>{$lng.lbl_no}</option>
</select>
{/if}
