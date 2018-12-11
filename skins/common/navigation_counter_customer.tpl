

<div class="navigation_counter">
{if $navigation.total_items gt 0}
{$lng.txt_N_results_found|substitute:"items":$navigation.total_items}
    {if $config.Appearance.infinite_scroll ne 'Y'}
        {$lng.txt_displaying_X_Y_results|substitute:"first_item":$navigation.first_item:"last_item":$navigation.last_item:"items":$navigation.total_items}
    {/if}
{else}
{$lng.txt_N_results_found|substitute:"items":0}
{/if}
</div>
