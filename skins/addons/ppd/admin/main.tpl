{if $addons.ppd && $app_area eq 'admin' && $current_target eq 'products'}
{if $action eq 'ppd_details'}
{include file='addons/ppd/admin/ppd_details.tpl'}
{else}
{include file='addons/ppd/admin/ppd_list.tpl'}
{include file='addons/ppd/admin/ppd_new.tpl'}
{/if}
{/if}