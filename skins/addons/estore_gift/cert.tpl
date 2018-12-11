{include_once file='js/check_email_script.tpl'}
{include_once file='main/include_js.tpl' src='js/register.js'}
<script type="text/javascript">
<!--
var txt_recipient_invalid 		= "{$lng.txt_recipient_invalid|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var txt_amount_invalid 			= "{$lng.txt_amount_invalid|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var txt_gc_enter_mail_address 	= "{$lng.txt_gc_enter_mail_address|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";

{if $usertype eq "C"}
	var orig_mode = "gc2cart";
{else}
	var orig_mode = "{$mode|escape:"javascript"}";
{/if}

{if $config.estore_gift.allow_customer_select_tpl eq "Y" and $usertype eq "C"}
	{assign var="allow_tpl" value='1'}
{else}
	{assign var="allow_tpl" value=''}
{/if}

var min_gc_amount 			= {$min_gc_amount|default:0};
var max_gc_amount 			= {$max_gc_amount|default:0};
var is_c_area 				= {if $usertype eq "C"}true{else}false{/if};
var enablePostMailGC 		= "{$config.estore_gift.enablePostMailGC}";
var required_field_empty 	= "{$lng.lbl_required_field_is_empty|strip_tags|escape:javascript}";
var lbl_gift_certificate 	= "{$lng.lbl_gift_certificate}";

{literal}
var gift_certificate_field_empty = substitute(required_field_empty, 'field', lbl_gift_certificate);

function check_gc_form() {

    if (document.gccreate.purchaser.value == "") {
        document.gccreate.purchaser.focus();
        alert(txt_gc_enter_mail_address);
        return false;
    }
	
	if (document.gccreate.recipient_to.value == "") {
		document.gccreate.recipient_to.focus();
		alert(txt_recipient_invalid);
		return false;
	}

	var num = convert_number(document.gccreate.amount.value);

	if (
		!check_is_number(document.gccreate.amount.value) 
		|| (is_c_area && (num < min_gc_amount || (max_gc_amount > 0 && num > max_gc_amount)))
	) {
		document.gccreate.amount.focus();
	    alert(txt_amount_invalid);
		return false;
	}

	if (enablePostMailGC == 'Y') {

		if (
			document.gccreate.send_via[0].checked 
			&& !checkEmailAddress(document.gccreate.recipient_email)
		) {
			document.gccreate.recipient_email.focus();
			return false;
		}

		if (
			document.gccreate.send_via[1].checked 
			&& (
				document.gccreate.recipient_firstname.value == "" 
				|| document.gccreate.recipient_lastname.value == "" 
				|| document.gccreate.recipient_address.value == "" 
				|| document.gccreate.recipient_city.value == "" 
				|| document.gccreate["recipient[state]"].value == "" 
				|| document.gccreate.recipient_zipcode.value == ""
			)
		) {
			document.gccreate.recipient_firstname.focus();
			alert(txt_gc_enter_mail_address);
			return false;
		}

	}
	else if (!checkEmailAddress(document.gccreate.recipient_email)) {
		document.gccreate.recipient_email.focus();
		return false;
	}

	return true;
}

function formSubmit() {
	if (check_gc_form()) {
		document.gccreate.action.value = orig_mode;
		document.gccreate.mode.value = orig_mode;
		document.gccreate.target = ''
		cw_submit_form(document.gccreate);
	}
}
-->
</script>
{/literal}

{if $config.estore_gift.enablePostMailGC eq "Y" && $allow_tpl}
<script type="text/javascript" language="JavaScript 1.2">
<!--
{literal}
$(document).ready(function() {
	switchPreview();
});

function switchPreview() {
	if (document.gccreate.send_via[0].checked) {
		document.getElementById('preview_button').style.display='none';
	    document.getElementById('preview_template').style.display='none';
	}
	if (document.gccreate.send_via[1].checked) {
		document.getElementById('preview_button').style.display='';
	    document.getElementById('preview_template').style.display='';
	}
}

function formPreview() {
	if (check_gc_form()) {
		document.gccreate.action.value='preview';
		document.gccreate.mode.value='preview';
		document.gccreate.target='_blank'
		cw_submit_form(document.gccreate);
	}
}
{/literal}
-->
</script>
{else}
<script type="text/javascript" language="JavaScript 1.2">
<!--
{literal}
function switchPreview() {
	return false;
}
{/literal}
-->
</script>
{/if}
<div class="gift_certificate">
{include file='js/check_zipcode_js.tpl'}

{if $customer_id and $usertype eq 'C'}
<div class="dialog_title">{$lng.txt_gift_certificate_checking_msg}</div>
{if $gc_id and !$gc_array}
<font class="fiend_error">{$lng.err_gc_not_found}</font>
{/if}
<form action="index.php?target={$current_target}&mode=giftcert" method="post" onsubmit="{literal}if($('#gc_chk_id').val()==''){alert(gift_certificate_field_empty);return false;}{/literal}">
<div class="input_field_1">
	<label>{$lng.lbl_gift_certificate}:</label>
	<input type="text" size="25" maxlength="16" name="gc_id" id="gc_chk_id" value="{$gc_id|escape:"html"}" />
	<input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}" />
</div>
</form>

{if $gc_array}
<hr size="1" noshade="noshade" />
<div class="input_field_1">
	<label>{$lng.lbl_gc_id}:</label>
	{$gc_array.gc_id}
</div>
<div class="input_field_1">
	<label>{$lng.lbl_amount}:</label>
	{include file='common/currency.tpl' value=$gc_array.amount}
</div>
<div class="input_field_1">
	<label>{$lng.lbl_remain}:</label>
	{include file='common/currency.tpl' value=$gc_array.debit}
</div>
<div class="input_field_1">
	<label>{$lng.lbl_status}:</label>
{if $gc_array.status eq "P"}{$lng.lbl_pending}
{elseif $gc_array.status eq "A"}{$lng.lbl_active}
{elseif $gc_array.status eq "B"}{$lng.lbl_blocked}
{elseif $gc_array.status eq "D"}{$lng.lbl_disabled}
{elseif $gc_array.status eq "E"}{$lng.lbl_expired}
{elseif $gc_array.status eq "U"}{$lng.lbl_used}
{/if}
</div>
{/if}
{/if}

{if $amount_error}
<p class="ErrorMessage">{$lng.txt_amount_invalid}</p>
{/if}

<form name="gccreate" action="index.php?target=gifts" method="post" onsubmit="javascript: return check_gc_form()">
<input type="hidden" name="gcindex" value="{$gcindex|escape:"html"}" />
<input type="hidden" name="action" value="gc2cart" />
<input type="hidden" name='mode' value='' />

<div class="input_field_1">
    <label>1. {$lng.lbl_gc_whom_sending}</label>
    {$lng.lbl_gc_whom_sending_subtitle}
</div>
<div class="input_field_1">
    <label class="required">{$lng.lbl_from}</label>
    <input type="text" name="purchaser" size="30" value="{if $giftcert.purchaser}{$giftcert.purchaser|escape:"html"}{else}{$userinfo.firstname}{if $userinfo.firstname ne ''} {/if}{$userinfo.lastname}{/if}" />
</div>
<div class="input_field_1">
    <label class="required">{$lng.lbl_to}</label>
    <input type="text" name="recipient_to" size="30" value="{$giftcert.recipient|escape:"html"}" />
</div>

<div class="input_field_1">
    <label>2. {$lng.lbl_gc_add_message}</label>
    {$lng.lbl_gc_add_message_subtitle}
</div>
<div class="input_field_1">
    <label>{$lng.lbl_message}</label>
    <textarea name="message" rows="8" cols="50">{$giftcert.message}</textarea>
</div>

<div class="input_field_1">
    <label class="required">3. {$lng.lbl_gc_choose_amount}</label>
    {$lng.lbl_gc_choose_amount_subtitle}
</div>
<div class="input_field_1">
    <label>&nbsp;</label>
    {$config.General.currency_symbol}
    <input type="text" name="amount" size="10" maxlength="9" value="{$giftcert.amount|formatprice}" />
{if $usertype eq "C" and ($min_gc_amount gt 0 or $max_gc_amount gt 0)}{$lng.lbl_gc_amount_msg} {if $min_gc_amount gt 0}{$lng.lbl_gc_from} {$config.General.currency_symbol}{$min_gc_amount|formatprice}{/if} {if $max_gc_amount gt 0}{$lng.lbl_gc_through} {$config.General.currency_symbol}{$max_gc_amount|formatprice}{/if}{/if}
</div>

<div class="input_field_1">
    <label>4. {$lng.lbl_gc_choose_delivery_method}</label>
</div>

<div class="input_field_1">
    {if $config.estore_gift.enablePostMailGC eq "Y"}
    <input id="gc_send_e" type="radio" name="send_via" value="E" onclick="switchPreview();"{if $giftcert.send_via ne "P"} checked="checked"{/if} />
    {else}
    <input type="hidden" name="send_via" value="E" />
    {/if}
    {$lng.lbl_gc_send_via_email}
</div>
<div class="input_field_1">
    <label class="required">{$lng.lbl_gc_enter_email}</label>
    <input type="email" name="recipient_email" size="30" value="{$giftcert.recipient_email}" />
</div>

{if $config.estore_gift.enablePostMailGC eq "Y"}
<div class="input_field_1">
    <input id="gc_send_p" type="radio" name="send_via" value="P" onclick="switchPreview();"{if $giftcert.send_via eq "P"} checked="checked"{/if} /></td>
    {$lng.lbl_gc_send_via_postal_mail}
</div>

<div class="input_field_1">
    <label class="required">{$lng.lbl_firstname}</label>
    <input type="text" name="recipient_firstname" size="30" value="{$giftcert.recipient_firstname}" />
</div>
<div class="input_field_1">
    <label class="required">{$lng.lbl_lastname}</label>
    <input type="text" name="recipient_lastname" size="30" value="{$giftcert.recipient_lastname}" />
</div>
<div class="input_field_1">
    <label class="required">{$lng.lbl_address}</label>
    <input type="text" name="recipient_address" size="40" value="{$giftcert.recipient_address}" />
</div>
<div class="input_field_1">
    <label class="required">{$lng.lbl_city}</label>
    <input type="text" name="recipient_city" size="30" value="{$giftcert.recipient_city}" />
</div>
<div class="input_field_1">
    <label class="required">{$lng.lbl_state}</label>
    {include file='main/map/_states.tpl' name="recipient[state]" id="recipient[state]" default=$giftcert.recipient_state}
</div>
<div class="input_field_1">
    <label class="required">{$lng.lbl_country}</label>
<select id="recipient_country" name="recipient[country]" size="1" onchange="cw_address_init(this.value, '', 'recipient[country]')">
{section name=country_idx loop=$countries}
<option value="{$countries[country_idx].country_code}"{if $giftcert.recipient_country eq $countries[country_idx].country_code} selected="selected"{elseif $countries[country_idx].country_code eq $config.General.default_country and $giftcert.recipient_country eq ""} selected="selected"{elseif $countries[country_idx].country_code eq $userinfo.country && $giftcert.recipient_country eq ""} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
</select>
</div>

<div class="input_field_1">
    <label class="required">{$lng.lbl_zipcode}</label>
    <input type="text" name="recipient_zipcode" size="30" value="{$giftcert.recipient_zipcode}" onchange="javascript: check_zip_code_field(document.forms['gccreate'].recipient.country, document.forms['gccreate'].recipient_zipcode);" />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_phone}</label>
    <input type="text" name="recipient_phone" size="30" value="{$giftcert.recipient_phone}" />
</div>

{if $allow_tpl}
<div class="input_field_1" id="preview_template">
    <label>{$lng.lbl_gc_template}</label>
    <select name="gc_template">
	{foreach from=$gc_templates item=gc_tpl}
		<option value="{$gc_tpl|escape}"{if $gc_tpl eq $giftcert.tpl_file or $giftcert.tpl_file eq "" and $gc_tpl eq $config.estore_gift.default_giftcert_template} selected="selected"{/if}>{$gc_tpl}</option>
	{/foreach}
	</select>
</div>
{/if}

{/if}
</form>
<div class="gift_buttons">
{if $allow_tpl}
	<div id="preview_button">
		{include file='buttons/button.tpl' button_title=$lng.lbl_preview href="javascript: void(formPreview());" style='button'}
	</div>
{/if}

{if $usertype eq "C" or ($usertype eq "P" and $addons.Simple_Mode ne "" and $mode eq "modify_gc")}
	{if $gcindex ne ""}
		{if $action eq "wl"}
			{include file="buttons/gc_update.tpl" href="javascript: orig_mode = 'addgc2wl'; formSubmit();"}
		{else}
			{include file="buttons/gc_update.tpl" href="javascript: formSubmit();"}
		{/if}
	{else}
		{include file='buttons/button.tpl' button_title=$lng.lbl_gc_add_to_cart href="javascript: formSubmit();" style='button'}
		{if $customer_id ne ""}
			{include file="buttons/button.tpl" button_title=$lng.lbl_add_2_wishlist href="javascript: orig_mode = 'addgc2wl'; formSubmit();"  style='button'}
		{/if}
	{/if}
{else}
	{include file='buttons/button.tpl' button_title=$lng.lbl_gc_create href="javascript: formSubmit();" style='button'}
{/if}
</div>
<div class="clear"></div>
</div>
