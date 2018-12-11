<input type="hidden" name="imgid" value="{$imgid}" />
<input type="hidden" name="id" value="{$id|default:$multiple_id}" />
<input type="hidden" name="source" value="" />
<input type="hidden" name="js_tab" id="form_js_tab" value="" />

{jstabs}
default_tab={$js_tab|default:"search_orders"}
default_template=main/image_selection/image_selection_tabs.tpl

[submit]
title="{$lng.lbl_apply}"
href="javascript: submitFormAjax('webmaster_modify_form');"

{if !$tabs || in_array('on_local',$tabs)}
[on_local]
title="{$lng.lbl_file_on_local_computer}"
{/if}
{*
{if !$tabs || in_array('on_server',$tabs)}
[on_server]
title="{$lng.lbl_file_on_server}"
{/if}

{if !$tabs || in_array('on_internet',$tabs)}
[on_internet]
title="{$lng.lbl_file_on_internet}"
{/if}
*}
{/jstabs}

{include file='tabs/js_tabs.tpl'}
