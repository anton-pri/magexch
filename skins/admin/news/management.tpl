{*include file='common/page_title.tpl' title=$lng.lbl_news_management*}
{capture name=section}
<div class="block">
	<div class="block-content block-content-full">
		{$lng.txt_news_management_top_text}
	</div>
</div>
<div class="block">
{jstabs}
default_tab={$js_tab|default:"details"}

[details]
title="{$lng.lbl_details}"
template=admin/news/details.tpl

[subscriptions]
title="{$lng.lbl_subscriptions}"
template=admin/news/subscribers.tpl

[messages]
title="{$lng.lbl_messages}"
template=admin/news/messages.tpl

{if $messageid}
[message]
title="{$lng.lbl_message}"
template=admin/news/message.tpl

{else}
[add_message]
title="{$lng.lbl_add_message}"
template=admin/news/message.tpl
{/if}

{/jstabs}
{include file="admin/tabs/js_tabs.tpl"}
</div>

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_news_management}
