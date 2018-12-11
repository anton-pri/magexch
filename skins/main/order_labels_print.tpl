{section name=oi loop=$orders_data}
{assign var=order value=$orders_data[oi].order}
{assign var=customer value=$orders_data[oi].customer}
{assign var=products value=$orders_data[oi].products}
{assign var=giftcerts value=$orders_data[oi].giftcerts}
<pre>{include file="main/order_label_print.tpl"}</pre>
======================================================
{/section}
