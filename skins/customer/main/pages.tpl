{capture name=section}
{if $page_data.content ne ''}
{if $config.General.parse_smarty_tags eq "Y"}
{eval var=$page_data.content}
{else}
{$page_data.content}
{/if}
{/if}
{/capture}
{include file="common/section.tpl" title=$page_data.title content=$smarty.capture.section extra='width="100%"'}
