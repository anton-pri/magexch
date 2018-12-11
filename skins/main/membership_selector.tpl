{if $field eq ''}{assign var="field" value="membershipids[]"}{/if}
{assign var="size" value=1}

{if $memberships}
{count assign="size" value=$memberships print=false}
{math assign="size" equation="x+1" x=$size}

{if $size > 5}
{assign var="size" value=5}
{/if}

{/if}

<select name="{$field}" multiple="multiple" size="{$size}" {if $read_only}disabled{/if}>
<option value="-1"{if $data.membershipids eq ""} selected="selected"{/if}>{$lng.lbl_all}</option>
{if $memberships}
{foreach from=$memberships item=v}
<option value="{$v.membershipid}"{if $data.membershipids ne "" && $data.membershipids[$v.membershipid] ne ''} selected="selected"{/if}>{$v.membership}</option>
{/foreach}
{/if}
</select>
{if $is_short ne 'Y'}<br />{$lng.lbl_hold_ctrl_key}{/if}
