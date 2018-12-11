{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}

<form action="{$form_url}/index.php?target=acc_manager" method="post" name="register_reseller_form">
<input type="hidden" name="action" value="register_reseller" />
<div class="acc_fields">

<div class="input_field_1">
    <label>{$lng.lbl_country} *</label>
    {include file='main/select/country.tpl' name='register[country]' value=$prefilled.country}
</div>
<div class="input_field_1">
    <label>{$lng.lbl_tax_number_reseller} *</label>
    <input type="text" name="register[tax_number]"  value="{$prefilled.tax_number|escape}" />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_email} *</label>
    <input type="text" name="register[email]"  value="{$prefilled.email|escape}" />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_password} *</label>
    <input type="password" name="register[password]"  maxlength="64" value="{$prefilled.password}" />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_confirm_password} *</label>
    <input type="password" name="register[password2]"  maxlength="64" value="{$prefilled.password2}" />
</div>
</div>
<div class="clear"></div>
<div class="acc-buttons">
{include file='buttons/submit.tpl' href="javascript:cw_submit_form('register_reseller_form')" style='button'}
</div>

</form>
