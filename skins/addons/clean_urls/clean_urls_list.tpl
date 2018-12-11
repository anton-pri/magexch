{capture name=section}
<div class="block">

{jstabs}
default_tab = {$js_tab|default:'clean_urls_list'}

[clean_urls_list]
title = "{$lng.lbl_clean_urls_list}"
template = "addons/clean_urls/list_list.tpl"

[history_clean_urls_list]
title = "{$lng.lbl_history_clean_urls_list}"
template = "addons/clean_urls/list_history_list.tpl"

{/jstabs}

{include file='admin/tabs/js_tabs.tpl'}
</div>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_clean_urls_list}
