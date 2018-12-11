{if $included_tab eq 'photos'}
{* start *}
<iframe width="100%" height="450" src="index.php?target={$current_target}&mode=photos&user={$user}"></iframe>

{elseif $included_tab eq 'discounts'}
{* start *}
<iframe width="100%" height="250" src="index.php?target={$current_target}&mode=discounts&user={$user}"></iframe>

{else}
{include file="main/users/sections/`$included_tab`.tpl"}

{/if}
