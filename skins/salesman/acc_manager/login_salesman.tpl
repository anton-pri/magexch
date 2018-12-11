{$lng.txt_secure_login_form}<p/>

<form action="{$current_location}/index.php?target={$current_target}" method="post" name="login_customer_form">
<input type="hidden" name="action" value="login_customer" />

<div class="input_field_1">
    <label>{$lng.lbl_email}</label>
    <input type="text" name="email" size="30" value="{$email|escape}"/>
</div>
<div class="input_field_1">
    <label>{$lng.lbl_password}</label>
    <input type="password" name="password" size="30" />
</div>
{if $error eq 'login_incorrect'}
<div class="field_error">{$lng.msg_err_login_incorrect}</div>
{/if}
{if $addons.image_verification and $show_antibot.on_login eq 'Y' and $login_antibot_on}
{include file='addons/image_verification/spambot_arrest.tpl' mode="advanced" id=$antibot_sections.on_login}
{/if}
{if $error eq 'antibot_error'}
<div class="field_error">{$lng.msg_err_antibot}</div>
{/if}
{include file='buttons/submit.tpl' href="javascript:cw_submit_form('login_customer_form')" style='btn'}
</form>
