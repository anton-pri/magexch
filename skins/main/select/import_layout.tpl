{include_once_src file='main/include_js.tpl' src='js/popup_import_layout.js'}

{tunnel func='cw_import_smarty_layouts' type=$type assign='layouts'}
<select name="{$name}"{if $onchange} onchange="{$onchange}"{/if}>
<option value="">{$lng.lbl_please_select}</option>
{foreach from=$layouts item=layout}
<option value="{$layout.layout_id}"{if $value eq $layout.layout_id} selected{/if}>{$layout.title}</option>
{/foreach}
</select>
<div class="clear"></div>
{if $is_modify}
{include file='buttons/button.tpl' button_title=$lng.lbl_change href="javascript: popup_import_layout('`$type`'); void(0);"}
{/if}
