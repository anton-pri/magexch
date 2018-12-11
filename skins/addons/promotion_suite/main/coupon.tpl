{if $info.extra.promotion_suite}
{foreach from=$info.extra.promotion_suite.supply item=data}
{if $data.C}Discount coupon "{$data.C}" issued for next purchase {/if}
{/foreach}
{/if}
