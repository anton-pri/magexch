{if $page_data.type eq 'staticpopup'}
{include file='addons/cms/customer/display_static_content.tpl'}
{else}
{capture name=section}
{include file='addons/cms/customer/display_static_content.tpl'}
{/capture}
{include file="common/section.tpl" title=$page_data.name content=$smarty.capture.section extra='width="100%"'}
{/if}
