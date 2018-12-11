<div class="dialog_title">{$lng.txt_secure_login_form}</div>

{capture name=section}
<form action="{$catalog_secure.$app_area}/index.php?target=login" method="post" name="main_login_form">
<input type="hidden" name="action" value="login" />

<div class="input_field_1">
	<label>{$lng.lbl_email}</label>
	<input type="text" name="email" />
</div>
<div class="input_field_1">
	<label>{$lng.lbl_password}</label>
    <input type="password" name="password" maxlength="64" />
</div>
{if $addons.image_verification and $show_antibot.on_login eq 'Y' and $login_antibot_on}
{include file='addons/image_verification/spambot_arrest.tpl' mode='advanced' id=$antibot_sections.on_login}
{/if}
{if $antibot_err}
<div class="field_error">{$lng.msg_err_antibot}</div>
{/if}
{include file='buttons/submit.tpl' href="javascript:cw_submit_form('main_login_form')"}
</form>
{/capture}
{include file='common/section.tpl' title=$lng.lbl_secure_login_form content=$smarty.capture.section}
