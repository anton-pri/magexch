{if $content_only}{$content}{else}
<div class="menu{if $style} {$style}{/if}"{if $id} id="{$id}"{/if}>
    <div class="title"><div class="title-link">{if $link_href}<a href="{$link_href}">{/if}{$title}{if $link_href}</a>{/if}</div></div>
    <div class="content"{if $id} id="content_{$id}"{/if}>{$content}</div>

{if $vendorid eq '75368'}{cms service_code="shopfront_PP_Link_Keyseller"}{/if}
</div>
{/if}
