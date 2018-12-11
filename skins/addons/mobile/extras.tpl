{if $orders_list}
{if $is_title}
<th>
    <i class="fa fa-mobile-phone fa-15x"></i>
</th>
{else}
<td>
{if $extras.order_from_mobile_host eq "1"}
    <i class="fa fa-mobile-phone fa-15x"></i>
{/if}
</td>
{/if}
{/if}
