{include file="mail/mail_header.tpl"}

<p />{$lng.eml_dear_customer},

<p />{$lng.eml_password_recovery_msg}

<p />
<table cellpadding="1" cellspacing="1">
{if $accounts}
{section name=acc_num loop=$accounts}
<tr>
<td><tt>{$lng.lbl_username}:</tt></td>
<td>&nbsp;</td>
<td><tt>{$accounts[acc_num].email}</tt></td>
</tr>
<tr>
<td><tt>{$lng.lbl_password}:</tt></td>
<td>&nbsp;</td>
<td><tt>{$accounts[acc_num].password}</tt></td>
</tr>
{/section}
{else}
<tr>
<td colspan="3"><tt>no data was found</tt></td>
</tr>
{/if}
</table>

{include file="mail/signature.tpl"}

