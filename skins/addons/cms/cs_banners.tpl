{*
vim: set ts=2 sw=2 sts=2 et:
*}

{if $mode eq "list"}
  {include file="addons/cms/cms_list.tpl" contentsections=$contentsections}
{elseif $mode eq "search"}
  {include file="addons/cms/cms_search.tpl" contentsections=$contentsections}
{/if}
