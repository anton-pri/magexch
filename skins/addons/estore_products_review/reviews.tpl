 

<script type="text/javascript">
<!--

var lbl_err_filling_form = "{$lng.err_filling_form|strip_tags|escape:javascript}";
var txt_rate_this_product = "{$lng.txt_rate_this_product}";
var customer_voting_avail = "{$config.estore_products_review.customer_voting}";
var required_review_Fields = [
                		["review_author", "{$lng.lbl_your_name|escape:javascript}"],
		                ["review_message", "{$lng.lbl_your_message|escape:javascript}"]
		            ];
{literal}
function show_review_management_dialog(r_id, p_id) {
    $('#edited_review_id').val(r_id);
    $("#review_management_dialog").html('<iframe id="review_management_modal_iframe" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" />').dialog("open");
    $("#review_management_modal_iframe").attr("src", current_location + "/index.php?target=estore_review_management&review_id=" + r_id + "&product_id=" + p_id);
    return false;
}

$(document).ready(function() {
    $("#review_management_dialog").dialog({
        autoOpen: false,
        modal	: true,
        height	: 380,
        width	: 720
    });
});

function review_management_callback() {
    $("#review_management_dialog").dialog("close");
    var id = $('#edited_review_id').val();
    ajaxGet(current_location + "/index.php?target=estore_reviews_management&review_id=" + id, 'customer_review_item_' + id);
}

function check_votes_is_set() {
    $("#votes_table_id div").each(function() {

        if ($(this).raty('score') == undefined) {
            return false;
        }
    });

    return true;
}

function check_reviews_form() {
	var result;

	result = checkRequired(required_review_Fields);

    if (customer_voting_avail == "Y" && !check_votes_is_set()) {
        result = false;
        alert(txt_rate_this_product);
    }

	if (result) {
		document.getElementById('reviewform').submit();		
	}
}

function review_vote(review_id, vote){
    review_selector = '#review_vote_'+review_id;
    if(vote=='like'){
        $('.dislike_btn').removeClass('dislike-h');
        $('like_btn').addClass('like-h');
    }else if(vote == 'dislike'){
        $('.like_btn').removeClass('like-h');
        $('dislike_btn').addClass('dislike-h');
    }
    $.ajax({
        type:"GET",
        url:"index.php?target=review_vote",
        data:'action='+vote+'&review_id='+review_id,
        dataType: 'json',
        success: function(data){
            if(data.err==0){return;}
            $(review_selector+' .like_count').html(data.p_vote);
            $(review_selector+' .dislike_count').html(data.n_vote);
            $(review_selector+' a div').removeClass('like_vote dislike_vote');
            $(review_selector+' a .'+vote+'_btn').addClass(vote+'_vote');
        }
    });
}

{/literal}
-->
</script>
{include_once_src file='main/include_js.tpl' src='js/check_required_fields_js.js'}
<div id="review_management_dialog" title="Review management"></div>
<input type="hidden" name="edited_review_id" id="edited_review_id" value="0" />
<!-- cw@reviews -->
<div class="reviews">
{*include file='common/subheader.tpl' title=$lng.lbl_customer_reviews*}
{if $reviews}
{*
<select name="sort" onchange="javascript: window.location.href='{$reviews_navigation.script|amp}{if $reviews_navigation.page gt 0}&page={$reviews_navigation.page}{/if}&rsort='+this.value;" class="sort">
    <option value="time&rsort_direction=1" {if $rsort eq 'time' && $rsort_direction}selected{/if}>Newest</option>
    <option value="time&rsort_direction=0" {if $rsort eq 'time'&& !$rsort_direction}selected{/if}>Oldest</option>
    <option value="rate&rsort_direction=1" {if $rsort eq 'rate' && $rsort_direction}selected{/if}>Best rated</option>
    <option value="rate&rsort_direction=0" {if $rsort eq 'rate'&& !$rsort_direction}selected{/if}>Worst rated</option>
    <option value="helpful&rsort_direction=1" {if $rsort eq 'helpful'}selected{/if}>Most useful</option>    
</select>

{include file='common/navigation_counter.tpl' navigation=$reviews_navigation}
{include file='common/navigation_customer.tpl' navigation=$reviews_navigation}
*}
{foreach from=$reviews item=r name=reviews}
    {assign var="review_item_id" value=0}
    {if
        ($r.customer_id eq $customer_id
        || $r.customer_id eq $extended_review_customer_id
        )
        && $avail_by_settings
        && $block_by_stop_list eq ""
    }
        {assign var="review_item_id" value=$r.review_id}
    {/if}
    <!-- cw@review [ -->
    <div id="customer_review_item_{$review_item_id}" {if $r.customer_id eq 0}class="admin_manual_review"{/if}>
        {include file="addons/estore_products_review/customer_reviews_management_item.tpl" review_item=$r}
    </div>
    <!-- cw@review ] -->
    {*if !$smarty.foreach.reviews.last}<div class="separator"></div>{/if*}{/foreach}
    {if $customer_id eq ""}<p>{$lng.lbl_please_login_to_add_review}.</p>{/if}
{else}
<p>{$lng.txt_no_customer_reviews} {if $customer_id eq ""}{$lng.be_first_rev}.{/if}</p>
{/if}
{if
    $user_is_purchasers
    && $avail_by_settings
    && $userreview eq ""
    && $block_by_stop_list eq ""
}
    <p>{$lng.txt_already_purchased}</p>
{/if}

{include file='addons/estore_products_review/new_review.tpl'}
</div>
