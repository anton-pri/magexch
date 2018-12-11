{if $href}
    <a href="{eval var=$href}" title="{$title|strip_tags}" class="top_section_tab{if $selected}_selected{/if}{if $class} {$class}{/if}" {if $onclick}onclick="{$onclick}" {/if}>
<span>{$title}</span></a>
{else}
    <div class="top_section_tab{if $selected}_selected{/if}{if $class} {$class}{/if}">
<span>{$title}</span></div>
{/if}
