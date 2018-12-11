
<div class='cc_info_row'>
    <label>{$lng.lbl_cc_type}</label>
    <select name="card_type" onchange="javascript: markCVV2(this)">
    {section name=card_type loop=$card_types}
    <option value="{$card_types[card_type].code}"{if $userinfo.card_type eq $card_types[card_type].code} selected="selected"{/if}>{$card_types[card_type].type}</option>
    {/section}
    </select>
</div>

<div class='cc_info_row'>
    <label>{$lng.lbl_cc_name_explanation}</label>
    {if $userinfo.main_address.firstname ne ''}{assign var="card_firstname" value=$userinfo.main_address.firstname}{else}{assign var="card_firstname" value=$userinfo.firstname}{/if}
    {if $userinfo.main_address.lastname ne ''}{assign var="card_lastname" value=$userinfo.main_address.lastname}{else}{assign var="card_lastname" value=$userinfo.lastname}{/if}
    <input type="text" name="card_name" size="25" maxlength="50" value="{if $userinfo.card_name ne ""}{$userinfo.card_name|escape}{else}{$card_firstname|escape}{if $card_firstname ne ''} {/if}{$card_lastname|escape}{/if}" />
</div>

<div class='cc_info_row'>
    <label>{$lng.lbl_cc_number_explanation}</label>
    <input type="text" name="card_number" size="25" maxlength="20" value="{$userinfo.card_number}" />
</div>

{if $config.General.uk_oriented_ccinfo eq "Y"}
<div class='cc_info_row'>
    <label>{$lng.lbl_cc_validfrom}</label>
    {html_select_date prefix="card_valid_from_" display_days=false start_year="-5" month_format="%m"}
</div>
{/if}

<div class='cc_info_row'>
    <label>{$lng.lbl_cc_expiration}</label>
    {html_select_date prefix="card_expire_" display_days=false end_year="+10" month_format="%m" time=$userinfo.card_expire_time}
</div>

{if $payment_data.ccinfo || (!$payment_data.ccinfo && $config.General.enable_manual_cc_cvv2 eq 'Y')}
<div class='cc_info_row'>
    <label>{$lng.lbl_cc_cvv2}</label>
	<input type='text' name="card_cvv2" size="4" maxlength="4" value="{if $smarty.get.err eq 'fields'}{$userinfo.card_cvv2}{/if}" />
{*    <a href="#;" onclick="cw_one_step_checkout_dialog('txt_cvv2')" class="PopupHelpLink"><img src="{$ImagesDir}/question.png" alt="{$lng.lbl_popup_help|escape}" /></a>*}
</div>
<div id="txt_cvv2" style="display: none;">
    {$lng.txt_what_is_cvv2}<br/>
    <img src="{$ImagesDir}/ccards-cvv2-aex.gif" alt="American Express" /><br /><br />
    <img src="{$ImagesDir}/ccards-cvv2-visa.gif" alt="VISA, MasterCard, Discover" />
</div>
{/if}

{if $config.General.uk_oriented_ccinfo eq "Y"}
<div class='cc_info_row'>
    <label>{$lng.lbl_cc_issueno}</label>
    <input type="text" name="card_issue_no" size="4" maxlength="2" value="" />
    <span>{$lng.lbl_cc_leave_empty}</span>
</div>
{/if}
