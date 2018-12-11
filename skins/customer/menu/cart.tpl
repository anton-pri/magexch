{capture name=menu}

{assign_session var='cart' assign='cart'}
{include file='customer/menu/minicart.tpl'}

<ul>
	<li><a href="{$current_location}/index.php?target=cart">{$lng.lbl_view_cart}</a></li>
	<li><a href="{$current_location}/index.php?target=cart">{$lng.lbl_checkout}</a></li>

	{if $customer_id}
		<li><a href="{$current_location}/index.php?target=docs_O">{$lng.lbl_orders_history}</a></li>
        {if $addons.quote_system}
		<li><a href="{$current_location}/index.php?target=docs_I&mode=search">{$lng.lbl_invoices}</a></li>
        {/if}

		{if $addons.RMA}
			<li><a href="{$current_location}/index.php?target=docs_F">{$lng.lbl_returns}</a></li>
			<li><a href="{$current_location}/index.php?target=docs_F&mode=add">{$lng.lbl_add_return}</a></li>
		{/if}
	{/if}

	{if $main_menu_list}
		{foreach from=$main_menu_list item=menu_item}
			<li><a href="{$current_location}/{$menu_item.path}" {if $menu_item.need_login}class='need_login'{/if}>{$menu_item.name}</a></li>
		{/foreach}
	{/if}
</ul>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_your_cart content=$smarty.capture.menu style='cart'}
