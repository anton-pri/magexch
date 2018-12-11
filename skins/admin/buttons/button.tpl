{strip}
{if !$acl || ($acl && $accl.$acl)}

<a class="btn btn-minw btn-default {$style} {$class}" href="{$href|default:"javascript: void(0);"|amp}" 
    {if $onclick ne ''} onclick="{$onclick}"{/if}
    {if $title ne ''} title="{$title|escape}"{/if}
    {if $target ne ''} target="{$target}"{/if}>
    {$button_title}
</a>

{/if}
{/strip}
