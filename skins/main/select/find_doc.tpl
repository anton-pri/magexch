{include_once_src file='main/include_js.tpl' src='js/popup_doc.js'}
{assign var="id" value=$name|id}
<input type="text" name="{$name}" id="{$id}" size="4"/>
{include file='main/select/doc_type.tpl' name="`$name`_type"}
{include file='buttons/button.tpl' button_title=$lng.lbl_find_doc onclick="javascript: cw_popup_doc('`$form`', '`$id`', '`$name`_type')"}
