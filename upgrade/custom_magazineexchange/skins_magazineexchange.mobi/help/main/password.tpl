 {$lng.txt_password_recover}
<p />
<form action="{pages_url var='help' section='password'}" method="post" name="processform">
<ul class="password_recover">
<li> 
  <label>
    {$lng.lbl_email}
    <font class="CustomerMessage">*</font>
  </label>
  <input type="text" name="email" size="30" value="{$get_email|escape:"html"}" />
</li>
{if $smarty.get.section eq "password_error"}
<li>
<div class="ErrorMessage">{$lng.txt_email_invalid}</div>
</li>
{/if}

<li>
{include file="buttons/submit.tpl" href="javascript: cw_submit_form(document.processform)"}
</li>

<div class="login_note">{$lng.lbl_problems_logging} <a href="">{$lng.lbl_get_help}</a></div>

</table>
<input type="hidden" name="action" value="recover_password" />
</form>
