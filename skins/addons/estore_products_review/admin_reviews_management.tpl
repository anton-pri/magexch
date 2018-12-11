{if $main eq "estore_reviews_management"}
	{capture name="main_section"}
{/if}
<form name="review_search_form" action="{$target_reviews_management}" method="post">
    <input type="hidden" name="action" value="search" />

    {capture name=section}

	<div class="form-horizontal">
        <div class="form-group">
            <label class="col-xs-12">{$lng.lbl_search_for_pattern}:</label>
            <div class="col-xs-12"><input class="form-control" type="text" name="review_data[search][substring]"  value="{$review_data.search.substring}" /></div>
        </div>
        <div class="form-group">
            <label class="col-xs-12">{$lng.lbl_search_in}:</label>
        <div class="col-xs-12">
{if $product.product_id eq ''}
            <label><input type="checkbox" name="review_data[search][by_sku]"{if $review_data.search.by_sku} checked="checked"{/if} />
                {$lng.lbl_sku}&nbsp;</label>
{/if}
            <label><input type="checkbox" name="review_data[search][by_customer]"{if $review_data.search.by_customer} checked="checked"{/if} />
                {$lng.lbl_customer}&nbsp;</label>

            <label><input type="checkbox" name="review_data[search][by_status]"{if $review_data.search.by_status} checked="checked"{/if} />
                {$lng.lbl_status}&nbsp;</label>

            <label><input type="checkbox" name="review_data[search][by_flag]"{if $review_data.search.by_flag} checked="checked"{/if} />
                {$lng.lbl_flag}&nbsp;</label>
        </div>
        </div>
    </div>
    <div class="buttons">
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_search href="javascript: cw_submit_form('review_search_form');" style="btn-green push-20 push-5-r"}
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_reset href="javascript: cw_submit_form('review_search_form', 'reset');" style="btn-danger push-20"}
    </div>
    {/capture}
    {include file='admin/wrappers/block.tpl' title=$lng.lbl_reviews_management content=$smarty.capture.section}
</form>

{capture name=block}

{include file='common/navigation_counter.tpl'}

{if $navigation.total_items gt 9}
    {include file='common/navigation.tpl'}
{/if}

<form action="{$target_reviews_management}" method="post" name="reviews_management_form">
    <input type="hidden" name="action" value="process" />
    {assign var="pagestr" value="`$target_reviews_management`"}
    <div class="box">
    <table class="table table-striped dataTable vertical-center" width="100%" cellspacing="5" cellpadding="5" id="review_items_table">
		<thead>
        <tr>
            <th><input type='checkbox' class='select_all' class_to_select='checked_review_item' /></th>
            <th>
                {if $review_data.sort_field eq "ctime"}
                    {include file="buttons/sort_pointer.tpl" dir=$review_data.sort_direction}&nbsp;
                    <a href="{$pagestr}&action=process&sort=ctime&direction={if $review_data.sort_direction eq 0}1{else}0{/if}&page={$review_data.page}">{$lng.lbl_date}</a>
                {else}
                    <a href="{$pagestr}&action=process&sort=ctime&direction=0&page={$review_data.page}">{$lng.lbl_date}</a>
                {/if}
            </th>
            <th>
                {if $review_data.sort_field eq "productcode"}
                    {include file="buttons/sort_pointer.tpl" dir=$review_data.sort_direction}&nbsp;
                    <a href="{$pagestr}&action=process&sort=productcode&direction={if $review_data.sort_direction eq 0}1{else}0{/if}&page={$review_data.page}">{$lng.lbl_sku}</a>
                {else}
                    <a href="{$pagestr}&action=process&sort=productcode&direction=0&page={$review_data.page}">{$lng.lbl_sku}</a>
                {/if}
            </th>
            <th>{$lng.lbl_customer}</th>
            <th>{$lng.lbl_rating}</th>
            <th>{$lng.lbl_title_and_review}</th>
            <th>
                {if $review_data.sort_field eq "status"}
                    {include file="buttons/sort_pointer.tpl" dir=$review_data.sort_direction}&nbsp;
                    <a href="{$pagestr}&action=process&sort=status&direction={if $review_data.sort_direction eq 0}1{else}0{/if}&page={$review_data.page}">{$lng.lbl_status}</a>
                {else}
                    <a href="{$pagestr}&action=process&sort=status&direction=0&page={$review_data.page}">{$lng.lbl_status}</a>
                {/if}
            </th>
            <th>{$lng.lbl_flag}</th>
        </tr>
        </thead>
            {if $reviews}
                {foreach from=$reviews item=r}
                    <tr{cycle values=', class="cycle"'} id="review_item_record_{$r.review_id}">
                        {include file='addons/estore_products_review/admin_reviews_management_item.tpl' review_item=$r}
                    </tr>
                {/foreach}
            {else}
                <tr>
                    <td align="center" colspan="9">
                        {$lng.lbl_no_items_available}
                    </td>
                </tr>
            {/if}
    </table>
    </div>


    {if $reviews}
    <div class="buttons">
        {include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('reviews_management_form', 'delete');" style="btn-green push-20"}
    </div>
    {/if}

</form>

{if $navigation.total_items gt 9}
    {include file='common/navigation.tpl'}
{/if}

{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_product_reviews content=$smarty.capture.block}

{if $use_add_form}
{capture name="addrev"}
    <form action="{$target_reviews_management}" method="post" name="reviews_add_form">
    <div class="box">

        <input type="hidden" name="action" value="add_reviews" />
        <input type="hidden" name="review_new[vote]" id="review_edit_vote" value="0" />
        <input type="hidden" name="product_id" value="{$product.product_id|default:0}" />
{if $product.product_id gt 0}
        <div class="rating_rev push-10"><span>{$lng.lbl_rating}: </span> <div id="vote_star"></div></div>
{/if}

        <table class="table table-striped dataTable" width="100%">
        <thead>
            <tr>
                <th style="width: 25%">{$lng.lbl_author}</th>
                <th style="width: 25%">{$lng.lbl_title_and_review}</th>
                <th style="width: 25%">{$lng.lbl_add_to}</th>
                <th style="width: 25%">{$lng.lbl_status}</th>
            </tr>
        </thead>
            <tr valign="top">
                <td><input class="form-control" type="text" size="25" name="review_new[email]" value="" style="width:auto;"/></td>
                <td><input class="form-control" type="text" size="25" name="review_new[main_title]" value="" style="width:auto;"/><br />
                 <textarea class="form-control" cols="35" rows="4" name="review_new[message]" style="width: auto"></textarea></td>
                <td><select class="form-control" name="review_new[addto]">
                    <option value="">...</option>
                    <option value="testimonials">{$lng.lbl_testimonials}</option>
                    <option value="stoplist">{$lng.lbl_stop_list}</option>
                </select></td>
                <td><select class="form-control" name="review_new[status]">
                    <option value="0">{$lng.lbl_pending}</option>
                    <option value="1" selected="selected">{$lng.lbl_approved}</option>
                    <option value="2">{$lng.lbl_declined}</option>
                </select></td>
            </tr>
        </table>

    </div>
    <div class="buttons">
        {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('reviews_add_form');" button_title=$lng.lbl_add style="btn-green push-20"}
    </div>
    </form>
{/capture}
{if $product.product_id gt 0}
  {assign var='add_new_rev_title' value=$lng.lbl_add_new_review}
{else}
  {assign var='add_new_rev_title' value=$lng.lbl_add_new_global_review|default:'Add new global site review'}
{/if}
{include file='admin/wrappers/block.tpl' title=$add_new_rev_title content=$smarty.capture.addrev}

{/if}

<div id="review_management_dialog" title="Review management"></div>

<input type="hidden" name="edited_review_id" id="edited_review_id" value="0" />

<script type="text/javascript">
    var SkinDir = "{$SkinDir}";

    var vote_5 = "{$lng.lbl_excellent}";
    var vote_4 = "{$lng.lbl_very_good}";
    var vote_3 = "{$lng.lbl_good}";
    var vote_2 = "{$lng.lbl_fair}";
    var vote_1 = "{$lng.lbl_poor}";

    {literal}
    function show_review_management_dialog(rid,pid) {
        $('#edited_review_id').val(rid);
        $("#review_management_dialog").html('<iframe id="review_management_modal_iframe" width="728" height="412" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" />').dialog("open");
        $("#review_management_modal_iframe").attr("src", current_location + "/index.php?target=estore_review_management&review_id=" + rid + "&product_id=" + pid);
        return false;
    }

    $(document).ready(function() {
        $("#review_management_dialog").dialog({
            autoOpen: false,
            modal	: true,
            height	: 450,
            width	: 730
        });

        $('#vote_star').raty({
            starOff : SkinDir + '/addons/estore_products_review/js/img/star_off.png',
            starOn  : SkinDir + '/addons/estore_products_review/js/img/star.png',
            hints   : [vote_1, vote_2, vote_3, vote_4, vote_5],
            width   : 150,
            click   : function(score, evt) {
                $('#review_edit_vote').val(score);
            }
        });
    });

    function review_management_callback() {
        $("#review_management_dialog").dialog("close");
        var id = $('#edited_review_id').val();
        ajaxGet(current_location + "/index.php?target=estore_reviews_management&review_id=" + id, 'review_items_table');
    }
    {/literal}
</script>
{if $main eq "estore_reviews_management"}
    {/capture}
    {include file='admin/wrappers/section.tpl' title=$lng.lbl_reviews_management content=$smarty.capture.main_section}
{/if}
