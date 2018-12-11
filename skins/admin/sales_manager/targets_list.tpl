{if $salesmen}
{foreach from=$salesmen item=salesman}
<a href="index.php?target=targets&user={$salesman.customer_id}" {cycle values=', class="cycle"'}>{$salesman.customer_id|user_title:'B'}</a><br/>
{/foreach}
{else}
<center>{$lng.lbl_not_found}</center>
{/if}
