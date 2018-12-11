{assign var='ps_addon_name' value='promotion_suite'}
{if $addons[$ps_addon_name] == 1 && $app_area eq 'admin' && $current_target eq 'promosuite'}
    {if $action eq 'list'}
        {include file='addons/promotion_suite/admin/offers_list.tpl'}
    {elseif $action eq 'details'}
        {include file='addons/promotion_suite/admin/offer.tpl'}
    {elseif $action eq 'form'}
        {include file='addons/promotion_suite/admin/new_offer.tpl'}
    {elseif $action eq 'add'}
        {include file='addons/promotion_suite/admin/new_offer.tpl'}
    {/if}
{/if}
