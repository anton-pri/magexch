{assign var='current_tab' value=0}

{foreach from=$sellers_data item=mag_seller}{if !$mag_seller.is_digital && $mag_seller.quantity>0}{assign var='current_tab' value=1}{/if}{/foreach}

{if !$current_tab}
    {foreach from=$sellers_data item=mag_seller}{if $mag_seller.is_digital}{assign var='current_tab' value=2}{/if}{/foreach}
{/if}

{if !$current_tab}
    {if $external_links}{foreach from=$external_links item=link}{assign var='current_tab' value=2}{/foreach}{/if}
{/if}

{if !$current_tab}{assign var='current_tab' value=1}{/if}

{jstabs name='product_data_customer'}
default_tab={$current_tab}
default_template="customer/products/me_prod_tabs.tpl"

[1]
title="{$lng.lbl_print_edition}"

[2]
title="{$lng.lbl_digital_edition}"


{/jstabs}
{include file='tabs/js_tabs.tpl'}
