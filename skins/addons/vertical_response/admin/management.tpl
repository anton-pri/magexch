{capture name=section}
{$lng.txt_news_management_top_text}

<br /><br />

{jstabs}
default_tab={$js_tab|default:"details"}

[details]
title="{$lng.lbl_details}"
template=admin/news/details.tpl

[subscriptions]
title="{$lng.lbl_subscriptions}"
template=admin/news/subscribers.tpl

[message]
title="{$lng.lbl_message}"
template=admin/news/message.tpl
{/jstabs}

<br/>
{include file="tabs/js_tabs.tpl"}
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_news_management}
