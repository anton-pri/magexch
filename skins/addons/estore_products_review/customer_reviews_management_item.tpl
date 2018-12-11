
<div class="input_field_1">
  <div class="float-left rev_name">
    <!-- cw@review_vote [ -->
    {if $config.estore_products_review.customer_voting eq "Y"}
            {if $review_item.attribute_votes}
                {foreach from=$review_item.attribute_votes item=attribute_vote}
                    <label class="vote_name">{$attribute_vote.name}</label>
                    {include file='addons/estore_products_review/product_rating.tpl' rating=$attribute_vote.vote}
                    <div class='clear'></div>
                {/foreach}
            {/if}
    {/if}
    <!-- cw@review_vote ] -->

    <!-- cw@review_name [ -->
    <label class="customer_name">{if $review_item.customer_id gt 0}<b>{tunnel func='cw_review_name_initials' via='cw_call' param1=$review_item.name|default:$review_item.email assign='rev_display_name'} {$rev_display_name|default:$lng.lbl_unknown}</b>{else}<b>{$lng.lbl_store_team_review}{/if}</b></label>
    <!-- cw@review_name ] -->

    <!-- cw@review_date [ -->
    <div class="rev_date">{$review_item.ctime|date_format:"%D"}</div>
    <!-- cw@review_date ] -->

  </div>
  <div class="rev_content float-left">
    {if
        $avail_by_settings
        && (
            $review_item.customer_id eq $customer_id
            || $review_item.customer_id eq $extended_review_customer_id
        )
        && $review_item.customer_id ne 0
        && $block_by_stop_list eq ""
    }
        <div style="float: right; padding: 0 10px;">
            <a href="javascript: void(0);" onclick="show_review_management_dialog({$review_item.review_id},{$review_item.product_id});" title="{$lng.lbl_modify_review|escape}">
                <img src="{$SkinDir}/images/icon_edit.gif">
            </a>
        </div>
    {/if}
    {$review_item.message|replace:"\n":"<br />"}<br />
    <!-- cw@review_useful [ -->

{*
    <div class="stat-cnt" id="review_vote_{$review_item.review_id}">
        <a style="margin-right: 10px;" href="javascript: void(0);" onclick="review_vote({$review_item.review_id},'like');">
            <div class="like_btn {if $review_item.customer_vote == 1} like_vote{/if}"></div><div class="like_count">{$review_item.p_vote}</div>
        </a>
        <a href="javascript: void(0);" onclick="review_vote({$review_item.review_id},'dislike');">
            <div class="dislike_btn {if $review_item.customer_vote == 2} dislike_vote{/if}"></div><div class="dislike_count">{$review_item.n_vote}</div>
        </a>
    </div><!-- /stat-cnt -->
*}
    <!-- cw@review_useful ] -->

  </div>
</div>
