<div class="login_block">
<div class="help_box">
  <h2>{$lng.lbl_password_recovery_title}</h2>
<div class="box_content">
{$lng.txt_password_recover}
</div>
<p />
<form action="{pages_url var='help' section='password'}" method="post" name="processform">
<table cellpadding="0" cellspacing="0">
<tr> 
<td height="10" width="78" class="FormButton"><b>{$lng.lbl_email}</b></td>
<td width="10" height="10"><font class="CustomerMessage" color="red">*</font></td>
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
<td width="282">{include file="buttons/submit.tpl" href="javascript: cw_submit_form(document.processform)"}<br></td>
</tr>
</table>
</div>
<div style="text-align:center;width:335px; height:15px; font-size:12pt;">&nbsp</div>

<div class="help_box" style="width: 435px; background: #f2f2f2;">
  <h2>{$lng.lbl_did_you_know}</h2>
<div class="box_content" style="background: #f2f2f2;">{$lng.txt_register_account_after_ordering}</div>
     
</div>
<div>
<img style="display: block;
	left: 450px;
	position: absolute;
	top: 25px;
	width: 514px;" src="/cw/skins_magazineexchange/images/Forgotten_Password_Avatar.png" alt="Use this page if you've forgotten your customer account password" width="514" height="388" class="hide_tablet">
</div>

</div>
<input type="hidden" name="action" value="recover_password" />
</form>
