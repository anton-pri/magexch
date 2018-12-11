

{if $config.estore_products_review.customer_voting eq "Y" || 
	($config.estore_products_review.customer_reviews eq "Y" && ($reviews ne "" || $config.estore_products_review.writing_reviews eq "A" || ($customer_id && $config.estore_products_review.writing_reviews eq "R")))}
{capture name=rev}
<div class="reviews_wrapper">  
  {*if $config.estore_products_review.customer_voting eq "Y"}
    <div class="product_vote">{include file='addons/estore_products_review/vote.tpl'}</div>
  {/if*}
  {if $config.estore_products_review.customer_reviews eq "Y"}
    {include file='addons/estore_products_review/reviews.tpl'}
  {/if}

  {if !$customer_id && $config.estore_products_review.writing_reviews eq "R"}
  <div class="reviews">{$lng.lbl_only_registered}</div>
  {/if}
</div>

{/capture}
{include file='common/section.tpl' title=$lng.lbl_customer_reviews content=$smarty.capture.rev}
{/if}

