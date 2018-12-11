<form action="{$current_location}/index.php?target=acc_manager" method="post" name="register_customer_form">
<input type="hidden" name="action" value="register_customer" />
<div class="input_field_1">
    <label>{$lng.lbl_email}</label>
    <input type="text" name="register[email]" size="30" value="{$prefilled.email|escape}" />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_password}</label>
    <input type="password" name="register[password]" size="30" maxlength="64" value="{$prefilled.password}" />
</div>
<div class="input_field_1">
    <label>{$lng.lbl_password}</label>
    <input type="password" name="register[password2]" size="30" maxlength="64" value="{$prefilled.password2}" />
</div>
{include file='buttons/submit.tpl' href="javascript:cw_submit_form('register_customer_form')" style='btn'}
</form>
