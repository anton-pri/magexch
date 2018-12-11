{if !$products || $config.Appearance.show_cart_summary ne 'Y'}
	{include file="addons/estore_gift/gc_cart.tpl" giftcerts_data=$cart.giftcerts}
{/if}