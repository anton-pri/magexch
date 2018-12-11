{* artem, TODO: check if it is used anywhere (once was used in acc_manager.tpl) *}

{$lng.lbl_reseller_login_instructions}<p/>
<form action="{$current_location}/index.php?target=acc_manager" method="post" name="login_reseller_form">
<input type="hidden" name="action" value="login_reseller" />

<div class="input_field_1">
    <label>{$lng.lbl_email}</label>
    <input type="text" name="email" size="30" value="{$email|escape}" />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_tax_number_reseller}</label>
    <input type="text" name="tax_number" size="30" value="{$tax_number|escape}" />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_password}</label>
    <input type="password" name="password" size="30" />
</div>
{if $error eq 'login_incorrect'}
<div class="field_error">{$lng.msg_err_login_incorrect}</div>
{/if}
{if $addons.image_verification and $show_antibot.on_login eq 'Y' and $login_antibot_on}
{include file='addons/image_verification/spambot_arrest.tpl' mode='advanced' id=$antibot_sections.on_login}
{/if}
{if $error eq 'antibot_error'}
<div class="field_error">{$lng.msg_err_antibot}</div>
{/if}

<div class="right_floated">
{capture name='page_url'}{pages_url var='help' section='password'}{/capture}
{include file='buttons/button.tpl' button_title=$lng.lbl_recover_password href=$smarty.capture.page_url style='btn'}
</div>

{include file='buttons/submit.tpl' href="javascript:cw_submit_form('login_reseller_form')" style='btn'}
</form>
