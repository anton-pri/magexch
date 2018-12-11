{$lng.txt_password_recover}here
<p />
<form action="{pages_url var='help' section='password'}" method="post" name="processform">
<table cellpadding="0" cellspacing="0">
<tr> 
<td height="10" width="78" class="FormButton">{$lng.lbl_email}</td>
<td width="10" height="10"><font class="CustomerMessage">*</font></td>
<td width="282" height="10"> 
  <input type="text" name="email" size="30" value="{$get_email|escape:"html"}" />
</td>
</tr>
{if $smarty.get.section eq "password_error"}
<tr>
<td width="78" class="FormButton" height="5">&nbsp;</td>
<td width="10" height="5">&nbsp;</td>
<td width="282" height="5" class="ErrorMessage">{$lng.txt_email_invalid}</td>
</tr>
{/if}
<tr>
<td width="78" class="FormButton" height="5">&nbsp;</td>
<td width="10" height="5">&nbsp;</td>
<td width="282" height="5">&nbsp;</td>
</tr>
<tr> 
<td width="78" class="FormButton">&nbsp;</td>
<td width="10">&nbsp;</td>
<td width="282">{include file="buttons/submit.tpl" href="javascript: cw_submit_form(document.processform)"}</td>
</tr>
</table>
<input type="hidden" name="action" value="recover_password" />
</form>
