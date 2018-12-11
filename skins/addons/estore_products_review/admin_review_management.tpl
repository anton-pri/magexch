<div id='review_management_container'>

    <form action="index.php?target=estore_review_management" method="post" name="review_management_form">
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="review_id" value="{$review.review_id}" />
        
        <table class="table table-striped dataTable">
            {foreach from=$attribute_votes item=av key=attr_id}
                <tr>
                    <td><span class="vote_name">{$av.name}</span></td>
                    <td>&nbsp;</td>
                    <td><div class='review_rates' attribute-id="{$attr_id}" vote='{$av.vote}'></div></td>
                </tr>
            {/foreach}
        </table>

        <table class="table table-striped dataTable" width="100%" cellspacing="5" cellpadding="5">
        	<thead>
            <tr>
                <th width="20%">{$lng.lbl_author}</th>
                <th width="50%">{$lng.lbl_title_and_review}</th>
                <th width="15%" class="text-center">{$lng.lbl_add_to}</th>
                <th width="15%" class="text-center">{$lng.lbl_status}</th>
            </tr>
            </thead>
            <tr>
                <td valign="top"><input type="text" class="form-control" size="25" name="review[name]" value="{$review.name|default:$review.email|default:$lng.lbl_unknown}" /><br /><nobr>{$lng.lbl_email}:</nobr> {$review.email}</td>
                <td><input type="text" size="25" class="form-control" name="review[main_title]" value="{$review.main_title}" style="width:95%"/><br />
                    <textarea cols="35" rows="5" class="form-control" name="review[message]" style="width:95%" >{$review.message}</textarea></td>
                <td valign="top"><select name="review[addto]" class="form-control">
                    <option value="">...</option>
                    <option {if $review.testimonials eq 1}selected="selected" {/if} value="testimonials">{$lng.lbl_testimonials}</option>
                    <option {if $review.stoplist eq 1}selected="selected" {/if} value="stoplist">{$lng.lbl_stop_list}</option>
                </select></td>
                <td valign="top"><select name="review[status]" class="form-control">
                    <option {if $review.status eq 0}selected="selected" {/if} value="0">{$lng.lbl_pending}</option>
                    <option {if $review.status eq 1}selected="selected" {/if} value="1">{$lng.lbl_approved}</option>
                    <option {if $review.status eq 2}selected="selected" {/if} value="2">{$lng.lbl_declined}</option>
                </select></td>
            </tr>
        </table>

        {include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript: void(0);" onclick="blockElements('review_management_container',true); submitFormAjax('review_management_form',review_management_complete);" style="btn-green"}

    </form>
</div>

<script type="text/javascript">
    <!--
	var vote_hints = [
	"{$lng.lbl_poor}",
	"{$lng.lbl_fair}",
	"{$lng.lbl_good}",
	"{$lng.lbl_very_good}",
    "{$lng.lbl_excellent}"
	]

{literal}
	$(document).ready(function() {
		 $('.review_rates').raty({
			starOff  : skin_dir + '/addons/estore_products_review/js/img/star_off.png',
			starOn   : skin_dir + '/addons/estore_products_review/js/img/star.png',
			hints    : vote_hints,
			width    : 150,
			score: function () { return $(this).attr('vote');},
			scoreName: function () { return 'rating['+$(this).attr('attribute-id')+']';}
		});
	});
    
    function review_management_complete() {
        blockElements('review_management_container',false);
        window.parent.review_management_callback();
    }
{/literal}
-->
</script>
