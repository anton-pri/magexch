<table width="100%" cellpadding="0" cellspacing="0" align="center">
<tr>
	<td class="PopupTitle"><center><b>{$lng.lbl_calendar_for}: {$user}</b></center></td>
</tr>
<tr>
    <td>
<form action="index.php?target=popup_calendar" method="post" name="app_frm">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="user" value="{$user}">

<br/>
{include file="common/dialog_message.tpl"}
</form>

    </td>
</tr>
</table>
