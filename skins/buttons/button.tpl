{if $current_area eq 'A'}
    {include file='admin/buttons/button.tpl}
{else}
{strip}
{if !$acl || ($acl && $accl.$acl)}
{if $style eq "button" or  $style eq "btn"}


<a href="{$href|default:"javascript: void(0);"|amp}" class="{$style} {$class}"
    {if $onclick ne ''} onclick="{$onclick}"{/if}
    {if $title ne ''} title="{$title|escape}"{/if}
    {if $target ne ''} target="{$target}"{/if}>
<span class="button-right">
<span class="button-left">{$button_title}</span>
</span>

</a>

{elseif $style eq "small"}

<a href="{$href|default:"javascript: void(0);"|amp}" class="button {$style} {$class}"
    {if $onclick ne ''} onclick="{$onclick}"{/if}
    {if $title ne ''} title="{$title|escape}"{/if}
    {if $target ne ''} target="{$target}"{/if}>
{$button_title}
</a>

{else}

<a class="button simple {$style} {$class}" href="{$href|default:"javascript: void(0);"|amp}"
	{if $onclick ne ''} onclick="{$onclick}"{/if}
	{if $title ne ''} title="{$title|escape}"{/if}
	{if $target ne ''} target="{$target}"{/if}><span>{$button_title}</span></a>
{/if}
{/if}
{/strip}
{/if}
