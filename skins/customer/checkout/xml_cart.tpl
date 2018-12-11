<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xml>
    {if $action eq 'simple_action'}
    <error><![CDATA[{if $top_message.content}<div>{$top_message.content}</div>{/if}]]></error>{/if}
    {if $top_message.anchor}<error_anchor><![CDATA[{$top_message.anchor}]]></error_anchor>{/if}
    <cart><![CDATA[{include file='customer/cart/contents.tpl'}]]></cart>
</xml>
