{* Describe an offer data *}
Offer $offer.offer_id $offer.title

Conditions:
	{foreach from=$offer.conditions key=type item=cond_data}
	{/foreach}

Bonuses:
	{foreach from=$offer.bonuses key=type item=bonus_data}

	{if $type eq 'F' && $bonus_data}
		Give free products
		{foreach from=$bonus_data key=pid item=qty}
			#{$pid} - {$qty} items
		{/foreach}
	{/if}

	{if $type eq 'S'}
		{if $bonus_data.apply == 1}Free shipping for whole cart{/if}
		{if $bonus_data.products}
			Free shipping for products
			{foreach from=$bonus_data.products key=pid item=qty}
				#{$pid} - {$qty} items
			{/foreach}
		{/if}
		{if $bonus_data.categories}
			Free shipping for any product from categories
			{foreach from=$bonus_data.categories key=cid item=qty}
				#{$cid} - {$qty} items
			{/foreach}			
		{/if}
		{if $bonus_data.methods}
			* Free shipping offered for following methods only
			{foreach from=$bonus_data.methods item=shipping_id}
				#{$shipping_id}
			{/foreach}			
		{/if}
	{/if}

	{if $type eq 'D'}
		Discount  {if $bonus_data.disctype eq 2}{$bonus_data.discount}%{else}${$bonus_data.discount}{/if} 
		{if $bonus_data.apply == 1} on whole cart{/if}
		{if $bonus_data.products}
			on products
			{foreach from=$bonus_data.products key=pid item=qty}
				#{$pid} - {$qty} items
			{/foreach}
		{/if}
		{if $bonus_data.categories}
			on any product from categories
			{foreach from=$bonus_data.categories key=cid item=qty}
				#{$cid} - {$qty} items
			{/foreach}			
		{/if}
	{/if}
	
	{if $type eq 'C'}
		Provide coupon "{$bonus_data}"
	{/if}

	{/foreach}
	
