<div class="box">
{if $included_tab eq 'photos'}
<iframe width="100%" height="630" src="index.php?target={$current_target}&mode=photos&user={$user}"></iframe>
{include file='main/users/sections/custom.tpl'}

{elseif $included_tab eq 'discounts'}
<iframe width="100%" height="250" src="index.php?target={$current_target}&mode=discounts&user={$user}"></iframe>
{include file='main/users/sections/custom.tpl'}

{else}

{include file="main/users/sections/`$included_tab`.tpl"}

{/if}
</div>
