<div id='review_management_container'>

    <form action="index.php?target=estore_review_management" method="post" name="review_management_form">
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="review_id" value="{$review.review_id}" />

        <table>
            {foreach from=$attribute_votes item=av key=attr_id}
                <tr>
                    <td><span class="vote_name">{$av.name}</span></td>
                    <td>&nbsp;</td>
                    <td><div class='review_rates' attribute-id="{$attr_id}" vote='{$av.vote}'></div></td>
                </tr>
            {/foreach}
        </table>

        <div style="height: 30px"></div>

        <table class="header" width="100%" cellspacing="5" cellpadding="5">
            <tr>
                <th width="30%">{$lng.lbl_author}</th>
                <th width="70%">{$lng.lbl_message}</th>
            </tr>
            <tr>
                <td valign="top"><input type="text" size="35" name="review[email]" value="{$review.email|default:$lng.lbl_unknown}" /></td>
                <td><textarea cols="42" rows="5" name="review[message]">{$review.message}</textarea></td>
            </tr>
        </table>

        {if $review.review_id}
            {include file='buttons/button.tpl' style='btn' button_title=$lng.lbl_save href="javascript: void(0);" onclick="blockElements('review_management_container',true); submitFormAjax('review_management_form', review_management_complete);"}
        {/if}

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
			starOff  : skin_dir + '/addons/estore_products_review/star_off.png',
			starOn   : skin_dir + '/addons/estore_products_review/star.png',
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

<style>
{literal}
#main_area{margin-bottom: 0;}
{/literal}
</style>
