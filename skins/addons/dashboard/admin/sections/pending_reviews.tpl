<div class='dashboard_pending_reviews'>
    Reviews in the status of pending: {$count_pending_reviews}<br>
    {if $count_pending_reviews gt 0}
        <a href="index.php?target=estore_reviews_management&action=search&review_data[search][by_status]=on&review_data[search][substring]=Pending">Open pending reviews</a>
    {/if}
</div>