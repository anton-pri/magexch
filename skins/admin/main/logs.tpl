{capture name=section}
<div class="block content-boxed">
{jstabs name='logs_info'}
default_tab={$js_tab|default:"search"}

[search]
title="{$lng.lbl_search}"
template="admin/main/logs_search.tpl"

[settings]
title="{$lng.lbl_settings}"
template="admin/main/logs_settings.tpl"

{/jstabs}
{include file='admin/tabs/js_tabs.tpl'}
</div>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_shop_logs}
