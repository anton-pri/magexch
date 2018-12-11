{strip}
{if !$acl || ($acl && $accl.$acl)}
{if $style}

<table class="black">
<tr><td>
<a href="{$href|default:"javascript: void(0);"|amp}" class="button {$style} {$class}"
    {if $onclick ne ''} onclick="{$onclick}"{/if}
    {if $title ne ''} title="{$title|escape}"{/if}
    {if $target ne ''} target="{$target}"{/if}>
<span class="button-right">
<span class="button-left" id="count_for_export">{$button_title} [0]</span>
</span>

</a>
</td></tr>
</table>

{else}

<a class="button simple {$class}" href="{$href|default:"javascript: void(0);"|amp}"
	{if $onclick ne ''} onclick="{$onclick}"{/if}
	{if $title ne ''} title="{$title|escape}"{/if}
	{if $target ne ''} target="{$target}"{/if}><span id="count_for_export">{$button_title} [0]</span></a>
{/if}
{/if}
{/strip}
