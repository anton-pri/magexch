{if $content_only}{$content}{else}
<div class="block block-themed animated fadeIn"{if $id} id="{$id}"{/if}>
    <div class="block-header bg-{$style}">
        <ul class="block-options">
            {include file="admin/options/`$options`.tpl"}
        </ul>
        <h3 class="block-title">{if $link_href}<a href="{$link_href}">{/if}{$title}{if $link_href}</a>{/if}</h3>
    </div>
    <div class="jasellerblock-content block-content-full block-content-narrow"{if $id} id="content_{$id}"{/if}>{$content}</div>
</div>
{/if}
