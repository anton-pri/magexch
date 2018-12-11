{if $addons.news and $newslists}

{if $hide_header eq ""}
<tr>
<td height="20" colspan="3"><b>{$lng.lbl_newsletter}</b><hr size="1" noshade="noshade" /></td>
</tr>
{/if}

<tr>
<td colspan="3">{$lng.lbl_newsletter_signup_text}</td>
</tr>

<tr>
<td colspan="2">&nbsp;</td>
<td width="50%">
<table border="0" width="100%">

{section name=idx loop=$newslists}
{assign var="listid" value=$newslists[idx].listid}
<tr>
<td><input type="checkbox" name="subscription[{$listid}]" {if $subscription[$listid] ne ""}checked{/if} /></td>
<td>{$newslists[idx].name}</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><i>{$newslists[idx].descr}</i></td>
</tr>
{/section}

</table>
</td>
</tr>

{/if}
