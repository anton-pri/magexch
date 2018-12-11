{tunnel func='cw\news\get_newslists' via='cw_call' assign="all_newslists"}
<select name="{$name}"{if $onchange} onchange="{$onchange}"{/if}{if $disabled} disabled{/if}>
<option value="0">{$lng.lbl_none}</option>
{foreach from=$all_newslists item=newslist}
<option value="{$newslist.list_id}"{if $newslist.list_id eq $value} selected="selected"{/if}>{$newslist.name}</option>
{/foreach}
</select>
