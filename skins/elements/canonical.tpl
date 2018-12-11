{if $usertype eq 'C' && $category_category_url ne ""}
<link rel="canonical" href="{$current_location}{$category_category_url}?view_all=all" />
{/if}
{if $usertype eq 'C' && $main eq "welcome"}
<link rel="canonical" href="{$current_location}/" />
{/if}
{if $usertype eq 'C' && $smarty.get.page eq 1 && $main eq 'search'}
<link rel="canonical" href="{$current_host_location}{build_url url=$navigation_script page=null}" />
{/if}
