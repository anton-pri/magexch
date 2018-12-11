{assign var='dod_addon_name' value='deal_of_day'}
{if $addons[$dod_addon_name] == 1 && $app_area eq 'admin' && $current_target eq 'deal_of_day'}
    {if $action eq 'list'}
        {include file='addons/deal_of_day/admin/generators_list.tpl'}
    {elseif $action eq 'details'}
        {include file='addons/deal_of_day/admin/generator.tpl'}
    {elseif $action eq 'form'}
        {include file='addons/deal_of_day/admin/new_generator.tpl'}
    {elseif $action eq 'add'}
        {include file='addons/deal_of_day/admin/new_generator.tpl'}
    {/if}
{/if}
