{if $seller_customer_id gt 0}
{tunnel func='cw_seller_get_info' via='cw_call' assign='seller_info' param1=$seller_customer_id}
<span class="SellerName">{if $no_link eq ''}<a title="All Magazines" href="index.php?cat={$config.custom_magazineexchange.magexch_default_root_category}&vendorid={$seller_info.customer_id}" style="color:blue">{/if}{$seller_info.name}{if $no_link eq ''}</a>{/if}</span>
{if $no_feedback eq ''}<br />
{if $seller_info.shopfront.holiday_settings eq 1}
<span class='holiday_settings_on'>{$lng.lbl_holiday_settings_on}</span>
{else}
{tunnel func='magexch_seller_total_rating' via='cw_call' assign='seller_rating_info' param1=$seller_customer_id}<span>Feedback: <span title="Percentage of positive ratings">{if $seller_rating_info.total_count gt 0}{$seller_rating_info.rating}%{else}-{/if}</span> (<span title="The total sum of all individual feedbacks ratings">{$seller_rating_info.score}</span>)</span>{/if}{/if}
{else}<i>undefined seller</i>{/if}
