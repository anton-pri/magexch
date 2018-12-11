{if $items}
{foreach from=$items item=item name=top_menu}
<li>
    <a href="{$item.link}"{if $item.rel ne ''} rel="{$item.rel}" {/if}{if $smarty.foreach.top_menu.last}class="nobg"{/if}{$item.target}>{$item.title}</a>
    {if $item.subitems}{if $item.lev lt 3}<ul>{include file='addons/top_menu/items.tpl' items=$item.subitems}</ul>{/if}{/if}
</li>
{/foreach}
{/if}
