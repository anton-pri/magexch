{if $addons.estore_gift or ($addons.manufacturers and $config.manufacturers.manufacturers_menu ne "Y")}
{capture name=menu}
{if $addons.manufacturers ne "" and $config.manufacturers.manufacturers_menu ne "Y"}
<a href="{pages_url var="manufacturers"}">{$lng.lbl_manufacturers}</a><br />
{/if}
{if $addons.estore_gift}
{include file='addons/estore_gift/gc_menu.tpl'}
{include file='addons/estore_gift/giftreg_menu.tpl'}
{/if}
{/capture}
{ include file='common/menu.tpl' title=$lng.lbl_special content=$smarty.capture.menu }
{/if}
