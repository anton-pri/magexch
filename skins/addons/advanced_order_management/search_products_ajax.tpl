{strip}
{ldelim}
"products":"{capture name=sub}{include file='addons/advanced_order_management/search_products_results.tpl'}{/capture}{$smarty.capture.sub|escape:"json"}"
{rdelim}
{/strip}
