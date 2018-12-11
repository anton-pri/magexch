
<div class='cc_info_row'>
    <label>{$lng.lbl_cc_type}</label>
    <select name="card_type" onchange="javascript: markCVV2(this)">
    {section name=card_type loop=$card_types}
    	<option value="{$card_types[card_type].code}"{if $userinfo.card_type eq $card_types[card_type].code} selected="selected"{/if}>{$card_types[card_type].type}</option>
    {/section}
    </select>
</div>

<div class='cc_info_row'>
    <label>{$lng.lbl_cc_number_explanation}</label>
    <input type="text" name="card_number" size="25" maxlength="20" value="{$userinfo.card_number}" />
</div>

<div class='cc_info_row'>
    <label>{$userinfo.main_address.firstname} {$lng.lbl_firstname}</label>
    {if $userinfo.main_address.firstname ne ''}{assign var="card_firstname" value=$userinfo.main_address.firstname}{else}{assign var="card_firstname" value=$userinfo.firstname}{/if}
    <input type="text" name="first_name" size="25" maxlength="50" value="{$card_firstname|escape}" />
</div>

<div class='cc_info_row'>
    <label>{$lng.lbl_lastname}</label>
    {if $userinfo.main_address.lastname ne ''}{assign var="card_lastname" value=$userinfo.main_address.lastname}{else}{assign var="card_lastname" value=$userinfo.lastname}{/if}
    <input type="text" name="last_name" size="25" maxlength="50" value="{$card_lastname|escape}" />
</div>

<div class='cc_info_row'>
    <label>{$lng.lbl_cc_expiration}</label>
    {html_select_date prefix="card_expire_" display_days=false end_year="+10" month_format="%m" time=$userinfo.card_expire_time}
</div>

{if $payment_data.ccinfo || (!$payment_data.ccinfo && $config.General.enable_manual_cc_cvv2 eq 'Y')}
<div class='cc_info_row'>
    <label>{$lng.lbl_cc_cvv2}</label>
	<input type="text" name="card_cvv2" size="4" maxlength="4" value="{if $smarty.get.err eq 'fields'}{$userinfo.card_cvv2}{/if}" />
    <a href="#;" onclick="cw_one_step_checkout_dialog('txt_cvv2')" class="PopupHelpLink"><img src="{$ImagesDir}/question.png" alt="{$lng.lbl_popup_help|escape}" /></a>
</div>
<div id="txt_cvv2" style="display: none;">
    {$lng.txt_what_is_cvv2}<br/>
    <img src="{$ImagesDir}/ccards-cvv2-aex.gif" alt="American Express" /><br /><br />
    <img src="{$ImagesDir}/ccards-cvv2-visa.gif" alt="VISA, MasterCard, Discover" />
</div>
{/if}

<div class='cc_info_row'>
    <label>{$lng.lbl_address}</label>
    {if $userinfo.b_address ne ''}{assign var="address" value=$userinfo.b_address}{else}{assign var="address" value=$userinfo.address}{/if}
    <input type="text" name="address" size="26" maxlength="60" value="{$address|escape}" />
</div>

<div class='cc_info_row'>
    <label>{$lng.lbl_city}</label>
    {if $userinfo.b_city ne ''}{assign var="city" value=$userinfo.b_city}{else}{assign var="city" value=$userinfo.city}{/if}
    <input type="text" name="city" size="15" maxlength="40" value="{$city|escape}" />
</div>

<div class='cc_info_row'>
    <label>{$lng.lbl_state}</label>
    {if $userinfo.b_state ne ''}{assign var="state" value=$userinfo.b_state}{else}{assign var="state" value=$userinfo.state}{/if}
    <input type="text" name="state" size="4" maxlength="40" value="{$state|escape}" />
</div>

<div class='cc_info_row'>
    <label>{$lng.lbl_zipcode}</label>
    {if $userinfo.b_zipcode ne ''}{assign var="zipcode" value=$userinfo.b_zipcode}{else}{assign var="zipcode" value=$userinfo.zipcode}{/if}
    <input type="text" name="zipcode" size="9" maxlength="20" value="{$zipcode|escape}" />
</div>

<div class='cc_info_row'>
    <label>{$lng.lbl_country}</label>
    {if $userinfo.b_country ne ''}{assign var="country" value=$userinfo.b_country}{else}{assign var="country" value=$userinfo.country}{/if}
    <input type="text" name="country" size="22" maxlength="60" value="{$country|escape}" />
</div>
