<li>
    <a href="{$catalogs.admin}/index.php"><i class="si si-speedometer"></i><span class="sidebar-mini-hide">Dashboard</span></a>
</li>
{if $items}
{foreach from=$items item=item name=admin_menu}
<li {if $item.selected}class="open"{/if}>
    <a href="{$item.link}" class="nav-submenu" data-toggle="nav-submenu"><i class="fa fa-{$item.title}"></i><span class="sidebar-mini-hide">{lng name=$item.title}<!--{$item.menu_id}--></span></a>
    {if $item.subitems}{include file='menu/subitems.tpl' items=$item.subitems}{/if}
</li>
{/foreach}
{/if}
