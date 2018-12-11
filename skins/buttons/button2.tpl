{strip}
{if !$acl || ($acl && $accl.$acl)}
{if $style}
<a href="{$href|default:"javascript: void(0);"|amp}" class="button {$style}"
    {if $onclick ne ''} onclick="{$onclick}"{/if}
    {if $title ne ''} title="{$title|escape}"{/if}
    {if $target ne ''} target="{$target}"{/if}>
<span class="btl">&nbsp;</span>
<span class="btc">{$button_title}</span>
<span class="btr">&nbsp;</span>
</a>
{else}
<a class="button simple" href="{$href|default:"javascript: void(0);"|amp}"
	{if $onclick ne ''} onclick="{$onclick}"{/if}
	{if $title ne ''} title="{$title|escape}"{/if}
	{if $target ne ''} target="{$target}"{/if}>{$button_title}</a>
{/if}
{/if}
{/strip}
