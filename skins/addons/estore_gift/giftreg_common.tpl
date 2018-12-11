{if $main_mode eq "manager"}
<p />
{include file="addons/estore_gift/events_list.tpl"}
<p />
{if $mode eq "maillist"}
{include file="addons/estore_gift/maillist.tpl"}
{elseif $mode eq "products"}
{include file="addons/estore_gift/products.tpl"}
{elseif $mode eq "send"}
{include file="addons/estore_gift/event_send.tpl"}
{elseif $mode eq "gb"}
{include file="addons/estore_gift/event_guestbook.tpl"}
{elseif $mode eq "modify"}
{include file="addons/estore_gift/event_modify.tpl"}
{/if}

{else}

{if $mode eq "event_details"}
{include file="addons/estore_gift/event_details_customer.tpl"}
{else}
{include file="addons/estore_gift/giftreg_search.tpl"}
{/if}

{/if}
