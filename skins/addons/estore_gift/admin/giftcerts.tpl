{if $mode eq 'add_gc' or ($mode eq 'modify_gc' and !$gc_readonly)}
	{include file='addons/estore_gift/admin/cert.tpl'}
{elseif $mode eq 'modify_gc'}
	{include file='addons/estore_gift/admin/gc_static.tpl'}
{else}
	{include file='addons/estore_gift/admin/gc_list.tpl'}
{/if}
