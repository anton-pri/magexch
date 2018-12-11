<table cellspacing="0" cellpadding="0" width="100%">

<tr>
    <td colspan="2">{include file="common/subheader.tpl" title=$lng.lbl_company}</td>
</tr>

<tr> 
    <td valign="top">{$lng.lbl_company_name}:</td>
    <td valign="top">{$company.company_name}</td>
</tr>
<tr> 
    <td valign="top">{$lng.lbl_address}:</td>
    <td valign="top">{$company.address} {$company.city} {$company.state} {$company.zipcode} {$company.country}</td>
</tr>
</table>
<br><br/>
