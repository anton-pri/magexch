{*include file='common/page_title.tpl' title=$lng.lbl_discount_coupons*}
{capture name=section}


<form action="index.php?target={$current_target}" method="post" name="coupons_form">
<input type="hidden" name="action" value="update" />

<div class="block">
<div class="block-content">
<p>{$lng.txt_discountcoupons_desc}</p>

<table class="table table-striped" width="100%">
<thead>
<tr>
	<th width="10"><input type='checkbox' class='select_all' class_to_select='coupons_item' /></th>
	<th width="15%">{$lng.lbl_coupon}</th>
	<th width="15%">{$lng.lbl_status}</th>
	<th width="15%" class="text-center">{$lng.lbl_coupon_disc}</th>
	<th width="15%" class="text-center">{$lng.lbl_coupon_min}</th>
	<th width="15%" class="text-center">{$lng.lbl_coupon_times}</th>
	<th width="25%" class="text-center">{$lng.lbl_coupon_expires}</th>
</tr>
</thead>

{if $coupons}
{foreach from=$coupons item=coupon}
<tr{cycle values=", class='cycle'"}>
	<td><input type="checkbox" name="posted_data[{$coupon.coupon}][to_delete]" class="coupons_item" /></td>
	<td>{$coupon.coupon}</td>
	<td>{include file='admin/select/coupon_status.tpl' name="posted_data[`$coupon.coupon`][status]" value=$coupon.status}</td>
	<td align="center">{if $coupon.coupon_type eq "absolute"}{include file='common/currency.tpl' value=$coupon.discount}{elseif  $coupon.coupon_type eq "percent"}{$coupon.discount|formatprice}%{else}{$lng.lbl_coupon_freeship}{/if}</td>
	<td align="center">{include file='common/currency.tpl' value=$coupon.minimum}</td>
	<td align="center">{if $coupon.per_user}{$coupon.times}/{$lng.lbl_coupon_per_user}{else}{$coupon.times_used}/{$coupon.times}{/if}</td>
	<td align="center" nowrap="nowrap">{$coupon.expire|date_format:$config.Appearance.datetime_format}</td>
</tr>

<tr {cycle values=", class='cycle'"}>
	<td colspan="7">
<p>
{if $coupon.product_id ne 0}
{$lng.lbl_coupon_contains_product|substitute:"product_id":$coupon.product_id}
{elseif $coupon.category_id ne 0}
{if $addons.Simple_Mode}
{if $coupon.recursive eq "Y"}
{$lng.lbl_coupon_contains_products_cat_rec_href|substitute:"category_id":$coupon.category_id:"path":$catalogs.admin}
{else}
{$lng.lbl_coupon_contains_products_cat_href|substitute:"category_id":$coupon.category_id:"path":$catalogs.admin}
{/if}
{else}
{if $coupon.recursive eq "Y"}
{$lng.lbl_coupon_contains_products_cat_rec|substitute:"category_id":$coupon.category_id}
{else}
{$lng.lbl_coupon_contains_products_cat|substitute:"category_id":$coupon.category_id}
{/if}
{/if}
{else}
{capture name=minamount}{include file='common/currency.tpl' value=$coupon.minimum}{/capture}
{$lng.lbl_coupon_greater_than|substitute:"amount":$smarty.capture.minamount}
{/if}
{if $coupon.coupon_type eq "absolute" and ($coupon.product_id ne 0 or $coupon.category_id ne 0)}
<br />
{if $coupon.product_id ne 0}
{if $coupon.apply_product_once}
{$lng.lbl_coupon_apply_once}
{else}
{$lng.lbl_coupon_apply_each_item}
{/if}
{elseif $coupon.category_id ne 0}
{if $coupon.apply_product_once and $coupon.apply_category_once}
{$lng.lbl_coupon_apply_once}
{elseif !$coupon.apply_product_once and !$coupon.apply_category_once}
{$lng.lbl_coupon_apply_each_item_cat}
{else}
{$lng.lbl_coupon_apply_each_title_cat}
{/if}
{/if}
{/if}

</p>
	</td>
</tr>

{/foreach}
{else}
<tr>
    <td colspan="6" align="center">{$lng.txt_no_discount_coupons}</td>
</tr>
{/if}
</table>


  <div class="buttons push-20">
    {if $coupons}
      {include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('coupons_form','update');" acl='__1209' style="btn-green push-5-r"}
      {include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript:cw_submit_form('coupons_form','delete');" acl='__1209' style="btn-danger push-5-r"}
    {/if}
      {include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=coupons&mode=add" acl='__1209' style="btn-green"}
  </div>
</div>
</div>

</form>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_discount_coupons}
