<hr noshade="noshade" size="1" align="left" />

<table cellpadding="0" cellspacing="0" width="500px">

<tr>
	<td colspan="2"><b>{$lng.lbl_personal_information}</b></td>
</tr>

{if $userinfo.email}
<tr>
	<td width="50%">{$lng.lbl_email}:</td>
	<td>{$userinfo.email}</td>
</tr>
{/if}
{if $is_new && $userinfo.password}
<tr>
	<td width="50%">{$lng.lbl_password}:</td>
	<td>{$userinfo.password}</td>
</tr>
{/if}
{if $userinfo.main_address.firstname}
<tr>
	<td width="50%">{$lng.lbl_firstname}:</td>
	<td>{$userinfo.main_address.firstname}</td>
</tr>
{/if}
{if $userinfo.main_address.lastname}
<tr>
	<td width="50%">{$lng.lbl_lastname}:</td>
	<td>{$userinfo.main_address.lastname}</td>
</tr>
{/if}
{if $userinfo.main_address.company}
<tr>
	<td width="50%">{$lng.lbl_company}:</td>
	<td>{$userinfo.main_address.company}</td>
</tr>
{/if}
{if $userinfo.additional_info.tax_number}
<tr>
	<td width="50%">{if $userinfo.usertype eq 'R'}{$lng.lbl_tax_number_reseller}{else}{$lng.lbl_tax_number}{/if}:</td>
	<td>{$userinfo.additional_info.tax_number}</td>
</tr>
{/if}

{if $userinfo.membership}
<tr>
	<td width="50%">{$lng.lbl_membership}:</td>
	<td>{$userinfo.membership}</td>
</tr>
{/if}
</table>

{if $userinfo.addresses}
<br />
<table cellpadding="0" cellspacing="0" width="500px">
<tr>
	<td colspan="2"><b>{$lng.lbl_address}</b></td>
</tr>
{foreach from=$userinfo.addresses item=address}
{if $profile_fields.address.address.is_avail || $profile_fields.address.address_2.is_avail}
<tr>
    <td width="50%">{$lng.lbl_address}</td>
    <td>{$address.address}{if $address.address_2}<br>{$address.address_2}{/if}</td>
</tr>
{/if}
{if $profile_fields.address.city.is_avail}
<tr>
    <td width="50%">{$lng.lbl_city}</td>
    <td>{$address.city}</td>
</tr>
{/if}
{if $profile_fields.address.county.is_avail and $config.General.use_counties eq "Y"}
<tr>
    <td width="50%">{$lng.lbl_county}</td>
    <td>{$address.county}</td>
</tr>
{/if}
{if $profile_fields.address.state.is_avail}
<tr>
    <td width="50%">{$lng.lbl_state}</td>
    <td>{$address.statename} {$address.state}</td>
</tr>
{/if}
{if $profile_fields.address.country.is_avail}
<tr>
    <td width="50%">{$lng.lbl_country}</td>
    <td>{$address.countryname}</td>
</tr>
{/if}
{if $profile_fields.address.zipcode.is_avail}
<tr>
    <td width="50%">{$lng.lbl_zipcode}</td>
    <td>{$address.zipcode}</td>
</tr>
{/if}
{if $profile_fields.address.phone.is_avail}
<tr>
    <td width="50%">{$lng.lbl_phone}</td>
    <td>{$address.phone}</td>
</tr>
{/if}
{if $profile_fields.address.fax.is_avail}
<tr>
    <td width="50%">{$lng.lbl_fax}</td>
    <td>{$address.fax}</td>
</tr>
{/if}
{/foreach}
</table>
{/if}
