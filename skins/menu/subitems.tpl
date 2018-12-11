{if $items}
<ul>
{foreach from=$items item=item name=admin_menu}
<li  {if $item.selected}class="open"{/if}>
    <a href="{$item.link}" {if $smarty.foreach.admin_menu.last}class="nobg"{/if}><span class="sidebar-mini-hide">{lng name=$item.title}<!--{$item.menu_id}--></span></a>
    {if $item.subitems}{include file='menu/subitems.tpl' items=$item.subitems}{/if}
</li>
{/foreach}
</ul>
{/if}
