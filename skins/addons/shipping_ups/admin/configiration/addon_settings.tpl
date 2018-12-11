{if !$config.shipping_ups.accesskey}
{if $ups_reg eq 1}

    {capture name=section}
    <form action="index.php?target={$current_target}&mode=addons&addon=shipping_ups&action=ups_agree" method="post" name="ups_reg_form">
    <font class="error">{$lng.txt_need_to_agree_license}</font>

    <pre>{$license}</pre>

    
    <input type="radio" name="confirmed" value="Y" />{$lng.lbl_yes_agree}<br/>
    <input type="radio" name="confirmed" value="N" />{$lng.lbl_no_not_agree}<br/>

    {include file="buttons/button.tpl" button_title=$lng.lbl_next href="javascript: cw_submit_form('ups_reg_form');"}</td>
    </form>
    {/capture}
    {include file="common/section.tpl" title=$title content=$smarty.capture.section}

{elseif $ups_reg eq 2}

    {capture name=section}
    <form action="index.php?target={$current_target}&mode=addons&addon=shipping_ups&action=ups_register" method="post" name="ups_reg_form">

    <div class="input_field_1">
        <label>{$lng.lbl_contact_name}</label>
        <input type="text" name="posted_data[contact_name]" size="32" maxlength="30" value="{$userinfo.contact_name|escape}" />
    </div>

    <div class="input_field_1">
        <label>{$lng.lbl_title}</label>
        <input type="text" name="posted_data[title_name]" size="32" maxlength="35" value="{$userinfo.title_name|escape}" />
    </div>

    <div class="input_field_1">
        <label>{$lng.lbl_company_name}</label>
        <input type="text" name="posted_data[company]" size="32" maxlength="35" value="{$userinfo.company|escape}" />
    </div>

    <div class="input_field_1">
        <label>{$lng.lbl_street_address}</label>
        <input type="text" name="posted_data[address]" size="32" maxlength="50" value="{$userinfo.address|escape}"  />
    </div>

    <div class="input_field_1">
        <label>{$lng.lbl_city}</label>
        <input type="text" name="posted_data[city]" size="32" maxlength="50" value="{$userinfo.city|escape}"  />
{if $reg_error ne "" and $userinfo.city eq ""}<font class="Star">&lt;&lt;</font>{/if}
    </div>

    <div class="input_field_1">
        <label>{$lng.lbl_state}</label>
        {include file='main/map/states.tpl' name="posted_data[state]" default=$userinfo.state required="Y"}
    </div>

    <div class="input_field_1">
        <label>{$lng.lbl_country}</label>
        {include file='main/map/countries.tpl' countries=$countries name="posted_data[country]" default=$userinfo.country state_name="posted_data[state]" state_enabled=1}
    </div>

    <div class="input_field_1">
        <label>{$lng.lbl_zipcode}</label>
        <input type="text" name="posted_data[postal_code]" size="32" maxlength="11" value="{$userinfo.postal_code|escape}" />
    </div>

    <div class="input_field_1">
        <label>{$lng.lbl_phone_number}</label>
        <input type="text" name="posted_data[phone]" size="32" maxlength="25" value="{$userinfo.phone|escape}" />
        <b>{$lng.txt_note}:</b> {$lng.txt_ups_phone_number}
    </div>

    <div class="input_field_1">
        <label>{$lng.lbl_web_site_url}</label>
        <input type="text" name="posted_data[url]" size="32" maxlength="254" value="{$userinfo.url|escape}"  />
    </div>

    <div class="input_field_1">
        <label>{$lng.lbl_email_address}</label>
        <input type="text" name="posted_data[email]" size="32" maxlength="50" value="{$userinfo.email|escape}"  />
    </div>

    <div class="input_field_0">
        <label>{$lng.lbl_ups_account_number}</label>
        <input type="text" name="posted_data[shipper_number]" size="32" maxlength="10" value="{$userinfo.shipper_number|escape}"  /> 
        {$lng.txt_ups_account_number_note}
    </div>

    <div>
        <label>{$lng.lbl_ups_reg_contact_me}</label>
        <input type="radio" id="software_installer_yes" name="posted_data[software_installer]" value="yes"{if $userinfo.software_installer eq "yes"} checked="checked"{/if} />{$lng.lbl_yes}&nbsp;&nbsp;
        <input type="radio" name="posted_data[software_installer]" value="no"{if $userinfo.software_installer eq "no"} checked="checked"{/if} />{$lng.lbl_no}
    </div>

    </form>
    {include file="buttons/button.tpl" button_title=$lng.lbl_next href="javascript: cw_submit_form('ups_reg_form');"}</td>
    {/capture}
    {include file="common/section.tpl" title=$title content=$smarty.capture.section}

{/if}
{/if}
