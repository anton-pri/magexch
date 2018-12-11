<table cellspacing="0" cellpadding="10" width="100%">
<tr>
    <td valign="top" width="50%">
{include file='common/subheader.tpl' title=$lng.lbl_billing_address}
{include file='main/users/address_label.tpl' address=$userinfo.main_address}
    </td>
    <td valign="top" width="50%">
{include file='common/subheader.tpl' title=$lng.lbl_shipping_address}
{include file='main/users/address_label.tpl' address=$userinfo.current_address}
    </td>
</tr>
</table>
