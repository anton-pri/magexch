{include_once_src file='main/include_js.tpl' src='js/multirow.js'}
{assign var='id' value=$name|id}
<table cellpadding="0" cellspacing="0">
<tr>
    <td id="{$id}_box_1"><input type="file" name="{$name}" id="{$id}_file_0" value="" {if $disabled} disabled{/if} />&nbsp;</td>
    <td id="{$id}_add_button">{include file='main/multirow_add.tpl' mark=$id}</td>
</tr>
</table>
