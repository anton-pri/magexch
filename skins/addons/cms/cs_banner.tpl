{*
vim: set ts=2 sw=2 sts=2 et:
*}

{if $mode eq "add"}
  {include file="addons/cms/cms_details.tpl" content_section=$content_section script="index.php?target=cms&amp;mode=add"}
{elseif $mode eq "update"}
  {include file="addons/cms/cms_details.tpl" content_section=$content_section script="index.php?target=cms&amp;mode=update" contentsection_id=$contentsection_id}
{/if}
