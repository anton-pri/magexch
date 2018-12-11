{if $addons.estore_products_review ne ""}

<div class="box">
<div class="dialog_title">{$lng.txt_adm_reviews_top_text}</div>

<form action="index.php?target={$current_target}" method="post" name="modify_review_form">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="action" value="update_reviews" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="ge_id" value="{$ge_id}" />

{if $ge_id}
<b>* {$lng.txt_note}:</b> {$lng.txt_edit_product_group}<br/>
{/if}

<table class="header" width="100%">
<tr>
	{if $ge_id}<th width="15">&nbsp;</th>{/if}
	<th width="15">&nbsp;</th>
	<th width="30%">{$lng.lbl_author}</th>
	<th width="50%">{$lng.lbl_message}</th>
    <th width="20%">{$lng.lbl_add_to}</th>
</tr>

{if $product_reviews}

{foreach from=$product_reviews item=r}
<tr valign="top"{cycle values=', class="cycle"'}>
	{if $ge_id}<td><input type="checkbox" value="1" name="fields[review][{$r.review_id}]" /></td>{/if}
	<td><input type="checkbox" value="Y" name="rids[{$r.review_id}]" /></td>
	<td><input type="text" size="32" name="reviews[{$r.review_id}][email]" value="{$r.email|default:$lng.lbl_unknown}"  /></td>
	<td><input type="text" size="32" name="reviews[{$r.review_id}][main_title]" value="{$r.main_title}"  /><br />
      <textarea cols="40" rows="6" name="reviews[{$r.review_id}][message]">{$r.message}</textarea></td>
    <td><select name="reviews[{$r.review_id}][addto]">
        <option value="">...</option>
        <option {if $r.testimonials eq 1}selected="selected" {/if} value="testimonials">{$lng.lbl_testimonials}</option>
        <option {if $r.stoplist eq 1}selected="selected" {/if} value="stoplist">{$lng.lbl_stop_list}</option>
    </select></td>
</tr>
{/foreach}
{else}
<tr>
	<td colspan="5" align="center">{$lng.txt_no_reviews}</td>
</tr>
{/if}

{if $accl.$page_acl}
<tr>
	<td colspan="5">{include file="common/subheader.tpl" title=$lng.lbl_add_new_review}</td>
</tr>

<tr valign="top">
	{if $ge_id}<td><input type="checkbox" value="1" name="fields[new_review]" /></td>{/if}
	<td>&nbsp;</td>
	<td><input type="text" size="14" name="review_new[email]" value="" /></td>
	<td><input type="text" size="14" name="review_new[main_title]" value="" /><br />
     <textarea cols="40" rows="6" name="review_new[message]"></textarea></td>
    <td><select name="review_new[addto]">
        <option value="">...</option>
        <option value="testimonials">{$lng.lbl_testimonials}</option>
        <option value="stoplist">{$lng.lbl_stop_list}</option>
    </select></td>
</tr>
{/if}
</table>

{include file='buttons/button.tpl' href="javascript:cw_submit_form('modify_review_form');" button_title=$lng.lbl_update acl=$page_acl}
{if $product_reviews}
{include file='buttons/button.tpl' href="javascript:cw_submit_form('modify_review_form', 'review_delete');" button_title=$lng.lbl_delete_selected acl=$page_acl}
{/if}
</form>
</div>
{/if}
