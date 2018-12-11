<div style="font-size: 12px;margin-bottom:7px">
               <span style="font-weight: bold; color: #ff0008;">More information:-</span>
               feedback from customers and information provided by the seller themselves
            </div>
<div style="padding: 10px; font-size: 12px; background: white;border:1px solid lightgray">
<div class="shopfront_top_box">
<table class="shopfront_top_info">
<tr>
  <td class="shopfront_top_info" style="border-right: 1px darkgray solid;">
    <div class="shopfront_seller_feedback_title">Seller Feedback</div>
    <div class="shopfront_score_rating">{tunnel func='magexch_seller_total_rating' via='cw_call' assign='seller_rating_info' param1=$vendorid}
Score: <a href='#shopfront_seller_reviews'><span title="The total sum of all individual feedbacks ratings" style="color:blue; font-weight:bold">{$seller_rating_info.score}</span></a>&nbsp;&nbsp;&nbsp;
Rating: <span title="Percentage of positive ratings" style="font-weight:bold">{if $seller_rating_info.total_count gt 0}{$seller_rating_info.rating}%{else}-{/if}</span>
    </div>
  </td>
  <td class="shopfront_top_info">
    <div class="shopfront_seller_holiday_title">{$lng.lbl_holiday_settings|default:'Holiday Settings'}</div>
    <div class="shopfront_seller_holiday">
      Status: <span style="color:red; font-weight:bold">{if $shopfront.holiday_settings eq 1}{$lng.lbl_hs_on|default:'ON'}{else}{$lng.lbl_hs_off|default:'OFF'}{/if}</span>&nbsp;&nbsp;&nbsp;
      Return Date: <span style="font-weight:bold">{if $shopfront.holiday_settings_return_date ne 0}{$shopfront.holiday_settings_return_date|date_format:"%d/%m/%Y"}{else}<i>undefined</i>{/if} </span>
    </div>
  </td>
</tr>
</table>
</div>
{if $shopfront.long_desc ne ''}
<hr>
<div class="shopfront_section_title">{$lng.lbl_seller_information|default:'Seller Information'}</div>
{$shopfront.long_desc|replace:"\n":"<br>"}
<br><br>
{/if}

{tunnel func='magexch_shopfront_feedbacks' via='cw_call' param1=$vendorid assign='shopfront_feedbacks'}
{if count($shopfront_feedbacks) > 0}
<hr>
<div class="shopfront_section_title" id='shopfront_seller_reviews'>{$lng.lbl_seller_reviews|default:'Seller Reviews'}</div>
  <table border="0" cellspacing="0" cellpadding="2" width="100%" class="SellerTable">
      <tr class="ProductTableHead">
	  <td align="center" width="90">Rating</td>
	  <td align="center" width="120">Transaction</td>
	  <td align="center">Customer Comment</td>
      </tr>
{foreach from=$shopfront_feedbacks item=fb}
{if $fb.rating eq 1}
  {assign var='rating_name' value='Positive'}
{elseif $fb.rating eq 0}
  {assign var='rating_name' value='Neutral'}
{else}
  {assign var='rating_name' value='Negative'}
{/if}
<tr>
     <td class="shopfront_rating_{$rating_name}">{$rating_name|upper}</td>
     <td align='center'>{$fb.date|date_format:"%d/%m/%Y"}</td>
     <td class="shopfront_review_comment">{$fb.review|stripslashes}</td>
</tr>
{/foreach}
</table>
{/if}
</div>
