{* Describe all applied offers *}
{if $offers}
{foreach from=$offers item=offer key=offer_id}
{include file='addons/promotion_suite/main/offer_description.tpl'}
{/foreach}
{/if}
