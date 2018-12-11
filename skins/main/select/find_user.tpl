{include_once_src file='main/include_js.tpl' src='js/popup_user.js'}
{assign var="id" value=$name|id}
<input type="text" size="10" name="{$name}" id="{$id}" value="{$value}" class="micro" />
{include file='buttons/button.tpl' button_title=$lng.lbl_find_user onclick="javascript: cw_popup_user('`$form`', '`$id`', '`$area`')"}
