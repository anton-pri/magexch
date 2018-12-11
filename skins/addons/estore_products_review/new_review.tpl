

{* New review form *}

{if $avail_by_settings
    and $block_by_stop_list eq ""
}
<div class="add_review">

<!-- cw@add_subheader [ -->

{include file='common/subheader.tpl' title=$lng.lbl_add_your_review}
<!-- cw@add_subheader ] -->


<script type="text/javascript">

var vote_5 = "{$lng.lbl_excellent}";
var vote_4 = "{$lng.lbl_very_good}";
var vote_3 = "{$lng.lbl_good}";
var vote_2 = "{$lng.lbl_fair}";
var vote_1 = "{$lng.lbl_poor}";

{literal}
$(document).ready(function() {
	 $('.review_rates').raty({
		starOff  : skin_dir + '/addons/estore_products_review/star_off.png',
		starOn   : skin_dir + '/addons/estore_products_review/star.png',
		hints    : [vote_1, vote_2, vote_3, vote_4, vote_5],
		width    : 150,
		scoreName: function () { return 'rating['+$(this).attr('attribute-id')+']';},
        score: function() { return $(this).attr('data-score');  }
	});
});
{/literal}
</script>


<form method="post" action="{pages_url var="product" product_id=$product.product_id}" id="reviewform">
	<input type="hidden" name="action" value="review" />

<!-- cw@add_vote [ -->

  {if $config.estore_products_review.customer_voting eq "Y"}
	<table id="votes_table_id">
		{foreach from=$product_rates item=av}
		<tr>
			<td><span class="vote_name">{$av.name}: </span></td>
			<td>&nbsp;</td>
			<td><div class='review_rates' attribute-id="{$av.attribute_id}" data-score='{$preset_rating[$av.attribute_id]|default:$review.rating[$av.attribute_id]}'></div></td>
		</tr>
		{/foreach}
	</table>
	{/if}

<!-- cw@add_vote ] -->

<!-- cw@add_fields [ -->

    <div class="rev_fields">
	<div class="input_field_1">
              <label>{$lng.lbl_author}</label>
              {if $customer_info eq '' && $review.author eq ''}
              {tunnel func='cw_user_get_addresses_smarty' customer_id=$customer_id main=1 assign='rev_addr'}
              {foreach from=$rev_addr item=addr}{assign var='customer_info' value=$addr}{/foreach}
              {/if}
		<input type="text" size="24" name="review_author" id="review_author" placeholder="{$lng.lbl_your_name}" 
        value = '{if $review.author}{$review.author}{else}{$customer_info.firstname|escape} {$customer_info.lastname|escape} {$customer_info.email|escape}{/if}' />
	    {if $review.author eq  '' and $review.error ne ''}<font class="field_error">&lt;&lt;</font>{/if}
	</div>
	<div class="input_field_1">
           <label>{$lng.lbl_comment}</label>
	    <textarea cols="40" rows="4" name="review_message" placeholder="{$lng.lbl_your_message}" id="review_message">{$review.message|escape}</textarea>
	    {if $review.message eq  '' and $review.error ne ''}
		<font class="field_error" style="vertical-align: top">&lt;&lt;</font>
	    {/if}
	</div>
	
	{assign var="antibot_err" value=$review.antibot_err}
	{if $addons.image_verification and $show_antibot.on_reviews eq 'Y'}
		{include file="addons/image_verification/spambot_arrest.tpl" mode="simple" id=$antibot_sections.on_reviews}
	{/if}
	
	{include file='buttons/button.tpl' button_title=$lng.lbl_add_review style='top' href="javascript: check_reviews_form();" style="button"}
    </div>
<!-- cw@add_fields ] -->

</form>
</div>

{/if}

