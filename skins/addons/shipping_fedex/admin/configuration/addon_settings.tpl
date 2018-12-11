{if $addon eq 'shipping_fedex' && $config.shipping_fedex.account_number ne ''}
    {if !$config.shipping_fedex.meter_number}

        <form method="post" action="index.php?target={$current_target}&mode=addons&addon=shipping_fedex&action=get_meter_number">
        <input type="hidden" name="carrier" value="FDX" />
        {$lng.txt_fedex_get_meter_number_note} <br /><br />

        {include file="common/subheader.tpl" title=$lng.lbl_fedex_general}

        <div class="input_field_1">
          <label>{$lng.lbl_fedex_person_name}</label>
          <input type="text" size="35" maxlength="35" name="posted_data[person_name]" value="{$prepared_user_data.person_name|escape}" />
        </div>

        <div class="input_field_0">
          <label>{$lng.lbl_fedex_company_name}</label>
          <input type="text" size="35" maxlength="35" name="posted_data[company_name]" value="{$prepared_user_data.company_name|escape}" />
        </div>

        <div class="input_field_1">
          <label>{$lng.lbl_fedex_phone}</label>
          <input type="text" size="35" maxlength="16" name="posted_data[phone_number]" value="{$prepared_user_data.phone_number|escape}" />
        </div>

        <div class="input_field_0">
          <label>{$lng.lbl_fedex_pager_number}</label>
          <input type="text" size="35" maxlength="16" name="posted_data[pager_number]" value="{$prepared_user_data.pager_number|escape}" />
        </div>

        <div class="input_field_0">
          <label>{$lng.lbl_fedex_fax}</label>
          <input type="text" size="35" maxlength="16" name="posted_data[fax_number]" value="{$prepared_user_data.fax_number|escape}" />
        </div>

        <div class="input_field_0">
            <label>{$lng.lbl_fedex_email}</label>
            <input type="text" size="35" maxlength="120" name="posted_data[email]" value="{$prepared_user_data.email|escape}" onchange="javascript: checkEmailAddress(this);" />
        </div>

        <div class="input_field_1">
          <label>{$lng.lbl_fedex_address}</label>
          <input type="text" size="35" maxlength="35" name="posted_data[address_1]" value="{$prepared_user_data.address_1|escape}" />
        </div>

        <div class="input_field_0">
            <label>{$lng.lbl_address_2}</label>
            <input type="text" size="35" maxlength="35" name="posted_data[address_2]" value="{$prepared_user_data.address_2|escape}" />
        </div>

        <div class="input_field_1">
          <label>{$lng.lbl_fedex_city}</label>
          <input type="text" size="35" maxlength="35" name="posted_data[city]" value="{$prepared_user_data.city|escape}" />
        </div>

        <div class="input_field_1">
            <label>{$lng.lbl_fedex_state}</label>
            {include file="main/select/state.tpl" name='posted_data[state]' default=$prepared_user_data.state required='N'}
        </div>

        <div class="input_field_1">
          <label>{$lng.lbl_fedex_zipcode}</label>
          <input type="text" size="35" maxlength="16" name="posted_data[zipcode]" value="{$prepared_user_data.zipcode|escape}" onchange="javascript: check_zip_code_field(this.form['posted_data[country]'], this);" />
        </div>

        <div class="input_field_1">
            <label>{$lng.lbl_fedex_country}</label>
            {include file="main/select/country.tpl" name='posted_data[country]' value=$prepared_user_data.country}
        </div>

            <input type="submit" value="{$lng.lbl_fedex_get_meter_number|escape}" name="get_meter_number" />
        </form>
    {else}
        {$lng.txt_fedex_clear_meter_number_note}<br /><br />
        <b>{$lng.lbl_fedex_meter_number}:</b> {$config.shipping_fedex.meter_number|default:"n/a"}<br /><br />
        <form method="post" action="index.php?target={$current_target}&mode=addons&addon=shipping_fedex&action=clear_meter_number">
        <input type="submit" value="{$lng.lbl_fedex_clear_meter_number|escape}" name="clear_meter_number" />
        </form>
    {/if}
    <hr />
{/if}
