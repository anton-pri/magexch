{if $page_data.content ne ''}
{if $page_data.parse_smarty_tags}
{eval var=$page_data.content}
{else}
{$page_data.content}
{/if}
{/if}
