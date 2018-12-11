<div class="push-20 navigation_total_items {if $navigation.total_items le 0}zero_items{/if}">
{if $navigation.total_items gt 0}
    {if $config.Appearance.infinite_scroll ne 'Y'}
        {$lng.txt_N_results_found|substitute:"items":$navigation.total_items}. {$lng.txt_displaying_X_Y_results|substitute:"first_item":$navigation.first_item:"last_item":$navigation.last_item:"items":$navigation.total_items}
    {else}
        {$lng.txt_N_results_found|substitute:"items":$navigation.total_items}
    {/if}
{else}
{$lng.txt_N_results_found|substitute:"items":0}
{/if}
</div>
