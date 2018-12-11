<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xml>
    <head_login><![CDATA[{include file='customer/menu/login.tpl'}]]></head_login>
    <head_register><![CDATA[{include file='customer/menu/register.tpl'}]]></head_register>
    <auth_menu><![CDATA[{include file='elements/auth_top.tpl'}]]></auth_menu>
    <cart_menu><![CDATA[{include file='customer/menu/cart.tpl' content_only=1}]]></cart_menu>
    <error><![CDATA[{if !$customer_id || $top_message.type eq 'E'}
    {if $top_message.content}<div>{$top_message.content}</div>{else}{$lng.err_invalid_login}{/if}
{/if}]]></error>
    {if $top_message.anchor}<error_anchor><![CDATA[{$top_message.anchor}]]></error_anchor>{/if}
    <message><![CDATA[{if $top_message.content}<div>{$top_message.content}</div>{/if}]]></message>
    <onestep_login><![CDATA[{include file='customer/checkout/login.tpl'}]]></onestep_login>
    <onestep_method><![CDATA[{include file='customer/checkout/method.tpl'}]]></onestep_method>
    <onestep_payment><![CDATA[{include file='customer/checkout/payment.tpl'}]]></onestep_payment>
</xml>
