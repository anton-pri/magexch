{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}

<script>
{literal}
$(document).ready(function(){
	$('form[name="register_customer_form"]').validate();
});
{/literal}
</script>

<form action="{$form_url}/index.php?target=acc_manager" method="post" name="register_customer_form">
<input type="hidden" name="action" value="register_customer" />
<div class="acc_fields">
<div class="input_field_1">
    <label class='required'>{$lng.lbl_email} </label>
    <input type="email" name="register[email]"  class='required{if $fill_error.email ne ''}error{/if}' value="{$prefilled.email|escape}" />{if $fill_error.email ne ''} <span class="field_error">&nbsp;&lt;&lt; {$lng.lbl_email_already_used}</span>{/if}
</div>
<div class="input_field_1">
    <label class='required'>{$lng.lbl_password} </label>
    <input type="password" name="register[password]" id='password' class='required' maxlength="64" value="{$prefilled.password}" />
</div>
<div class="input_field_1">
    <label class='required'>{$lng.lbl_confirm_password} </label>
    <input type="password" name="register[password2]"  equalTo='#password' class='required' maxlength="64" value="{$prefilled.password2}" />
</div>
</div>

{if $addons.image_verification and $show_antibot.on_registration eq 'Y'}
{include file='addons/image_verification/spambot_arrest.tpl' mode='simple' id=$antibot_sections.on_registration}
{/if}
<div style="text-align: center;">{$lng.lbl_reg_terms}</div>

<div class="clear"></div>
<div class="acc-buttons">
{if $main eq 'checkout'}
{include file='buttons/button.tpl' href="javascript:cw_submit_form('register_customer_form')" button_title=$lng.lbl_checkout style='button'}
{else}
{include file='buttons/button.tpl' href="javascript:cw_submit_form('register_customer_form')" button_title=$lng.lbl_submit style='button'}
{/if}
</div>
<input type='submit' style='display:none;' hidefocus="true" tabindex="-1" />
</form>
