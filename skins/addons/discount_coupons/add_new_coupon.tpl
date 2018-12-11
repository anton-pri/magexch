{if $accl.__1209}
{*include file='common/subheader.tpl' title=$lng.lbl_coupon_add_new*}
{capture name=section}
<div class="box">

<form action="index.php?target={$current_target}" method="post" name="coupon_form">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="mode" value="add" />

<div class="input_field_1">
    <label>{$lng.lbl_coupon_code}</label>
	<input type="text" size="25" maxlength="16" name="add_coupon[coupon]" value="{$coupon_data.coupon}" />
    {if $smarty.get.error eq "coupon_already_exists"}<font class="ErrorMessage"> &lt;&lt; {$lng.lbl_coupon_already_exists}</font>{/if}
</div>

<div class="input_field_0">
	<label>{$lng.lbl_coupon_type}</label>
	<select name="add_coupon[coupon_type]">
	<option value="percent"{if $coupon_data.coupon_type eq "percent"} selected="selected"{/if}>{$lng.lbl_coupon_type_percent}</option>
	<option value="absolute"{if $coupon_data.coupon_type eq "absolute"} selected="selected"{/if}>{$config.General.currency_symbol} {$lng.lbl_coupon_type_absolute}</option>
	<option value="free_ship"{if $coupon_data.coupon_type eq "free_ship"} selected="selected"{/if}>{$lng.lbl_coupon_freeshiping}</option>
	</select>
</div>

<div class="input_field_1">
	<label>{$lng.lbl_discount}</label>
	<input type="text" size="25" name="add_coupon[discount]" value="{$coupon_data.discount|default:0|formatprice}" />
</div>

<div class="input_field_1">
	<label>{$lng.lbl_coupon_times_to_use}</label>
    <input type="text" size="8" name="add_coupon[times]" value="{$coupon_data.times|default:'1'}" class="micro" />
    <label style="float: none;display:inline-block;">
    <input type="checkbox" name="add_coupon[per_user]"{if $coupon_data.per_user} checked="checked"{/if} value="1"/>
    {$lng.lbl_coupon_per_customer}
    </label>
</div>

<div class="input_field_0">
	<label>{$lng.lbl_active}</label>
    {include file='main/select/yes_no.tpl' name='add_coupon[status]' value=$coupon_data.status|default:1}
</div>

<div class="input_field_0">
	<label>{$lng.lbl_coupon_expires}</label>
    {include file='main/select/date.tpl' name='add_coupon[expire]' value=$coupon_data.expire}
</div>

{include file='common/subheader.tpl' title=$lng.lbl_coupon_apply_to}

<div class="input_field_0">
       <label style="width:auto;">
	<input type="radio" name="add_coupon[apply_to]" value=""{if !$coupon_data.apply_to} checked="checked"{/if} />
	{$lng.lbl_coupon_apply_order_subtotal} ({$config.General.currency_symbol})
       </label>
	<input type="text" size="24" name="add_coupon[minimum]" value="" class="short" />
</div>

<div class="input_field_0">
    <label style="width:auto;">
		<input type="radio" name="add_coupon[apply_to]" value="product"{if $coupon_data.apply_to eq "product"} checked="checked"{/if}>
		{$lng.lbl_coupon_apply_product}
    </label>
	{product_selector name_for_id="add_coupon[product_id]" name_for_name="add_couponproduct_id_name"}
</div>
<div class="input_field_0">
    <label style="width:100%;">
    {$lng.lbl_coupon_apply_once}
    <input type="radio" name="add_coupon[how_to_apply_p]" value="1"{if $coupon_data.how_to_apply_p or $coupon_data.how_to_apply_p eq ""} checked="checked"{/if} /><br />
    </label>
    <label style="width:100%;">
    {$lng.lbl_coupon_apply_each_item}
    <input type="radio" name="add_coupon[how_to_apply_p]" value="0"{if $coupon_data.how_to_apply_p eq 0} checked="checked"{/if} />
    </label>
</div>

<div class="input_field_0">
    <label style="width:auto;">
	<input type="radio" name="add_coupon[apply_to]" value="category"{if $coupon_data.apply_to eq "category"} checked="checked"{/if}>
	{$lng.lbl_coupon_apply_category}
    </label>
       {include file='main/select/category.tpl' name="add_coupon[category_id]"}<br/>

    <label style="width:100%;">
	{$lng.lbl_coupon_apply_category_rec}
	<input type="checkbox" id="recursive" name="add_coupon[recursive]"{if $coupon_data.recursive} checked="checked"{/if} value="1" /><br />
    </label>

    <label style="width:100%;">
    {$lng.lbl_coupon_apply_once}
	<input type="radio" name="add_coupon[how_to_apply_c]" value="1"{if $coupon_data.how_to_apply_c or $coupon_data.how_to_apply_c eq ""} checked="checked"{/if} /><br />
    </label>

    <label style="width:100%;">
    {$lng.lbl_coupon_apply_each_item_cat}
	<input type="radio" name="add_coupon[how_to_apply_c]" value="2"{if $coupon_data.how_to_apply_c eq 2} checked="checked"{/if} /><br />
    </label>

    <label style="width:100%;">
    {$lng.lbl_coupon_apply_each_title_cat}
	<input type="radio" name="add_coupon[how_to_apply_c]" value="3"{if $coupon_data.how_to_apply_c eq 3} checked="checked"{/if} />
    </label>
</div>

</div>

<p>{$lng.txt_coupon_note}</p>
<div class="buttons">{include file='buttons/button.tpl' button_title=$lng.lbl_add href="javascript:cw_submit_form('coupon_form');"  style="btn"}</div>
{/if}

</form>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_coupon_add_new}
