{*HI, this is seller feedback popup.
{$seller_feedback|@debug_print_var}
*}
<span style="margin: 25px;" class="seller_feedback_info">{$lng.txt_seller_feedback_top_text|default:'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod#
 tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam'}</span>
<p />
<div class="seller_feedback_info">

<div class="seller_feedback_info_box">
<form name='save_seller_feedback' method="post" action='index.php?target=magexch_seller_feedback'>
<input type='hidden' name='action' value='save_seller_feedback' />
<input type='hidden' name='order_seller_id' value='{$order_seller_id}' />
<input type='hidden' name='doc_id' value='{$order.doc_id}' />
<table style="width:100%">
<tr><td colspan="2" class="seller_feedback_seller_name">Order: #{$order.display_id} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Seller: {include file="main/seller_info.tpl" seller_customer_id=$order_seller_id no_feedback=1 no_link=1}</td></tr>
{if $seller_feedback.customer_id eq ''}
<tr>
    <td>My Rating:</td>
    <td>
       <input type='radio' name='seller_rating' value='1' checked='checked' />&nbsp;Positive&nbsp;&nbsp;
       <input type='radio' name='seller_rating' value='0' />&nbsp;Neutral&nbsp;&nbsp;
       <input type='radio' name='seller_rating' value='-1' />&nbsp;Negative&nbsp;&nbsp;
    </td>
</tr>
<tr>
    <td>My Comment:</td>
    <td><input type="text" name='seller_review' value='' style="width:100%"></td>
</tr>
{else}
<tr>
    <td>My Rating:</td>
    <td>
       <input type='radio' name='seller_rating' value='1' disabled="disabled" {if $seller_feedback.rating eq 1}checked="checked"{/if} />&nbsp;Positive&nbsp;&nbsp;
       <input type='radio' name='seller_rating' value='0' disabled="disabled" {if $seller_feedback.rating eq 0}checked="checked"{/if} />&nbsp;Neutral&nbsp;&nbsp;
       <input type='radio' name='seller_rating' value='-1' disabled="disabled" {if $seller_feedback.rating eq -1}checked="checked"{/if} />&nbsp;Negative&nbsp;&nbsp;
    </td>
</tr>
<tr>
    <td>My Comment:</td>
    <td>{$seller_feedback.review|stripslashes}</td>
</tr>
{/if}

</table>
</form>
</div>
<div style="text-align: center">
{if $seller_feedback.customer_id eq ''}
    {include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript: cw_submit_form('save_seller_feedback');" style="button"}
{else}
    {include file='buttons/button.tpl' button_title=$lng.lbl_back_feedback|default:'< Back' href="javascript: $('.ui-dialog-titlebar-close').click();" style="button"}
{/if}
</div>
<p />
<p />
<div style="text-align: center">
<span>{$lng.txt_once_feedback_is_left|default:'Once feedback has been left you can\'t edit or delete it'}</span>
</div>
</div>
