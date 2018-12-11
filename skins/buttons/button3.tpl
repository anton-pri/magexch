{strip}
{if !$acl || ($acl && $accl.$acl)}


<table class="black2" cellspacing="0" cellpadding="0">
<tr><td>
<a href="{$href|default:"javascript: void(0);"|amp}" class="button {$style}"
    {if $onclick ne ''} onclick="{$onclick}"{/if}
    {if $title ne ''} title="{$title|escape}"{/if}
    {if $target ne ''} target="{$target}"{/if}>
<span class="button-right">
<span class="button-left">{$button_title}</span>
</span>

</a>
</td></tr>
</table>


{/if}
{/strip}
