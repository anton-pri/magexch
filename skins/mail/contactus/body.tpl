{include file="mail/mail_header.tpl"}
<p />{$lng.eml_customers_need_help}

<p /><b>{$lng.lbl_customer_info}:</b>

<hr size="1" noshade="noshade" />

<table cellpadding="2" cellspacing="0">
{if $profile_fields.title.avail}
<tr>
<td><b>{$lng.lbl_company}:</b></td>
<td>&nbsp;</td>
<td>{$contact.company}</td>
</tr>
{/if}
{if $profile_fields.firstname.avail}
<tr>
<td><b>{$lng.lbl_firstname}:</b></td>
<td>&nbsp;</td>
<td>{$contact.firstname}</td>
</tr>
{/if}
{if $profile_fields.lastname.avail}
<tr>
<td><b>{$lng.lbl_lastname}:</b></td>
<td>&nbsp;</td>
<td>{$contact.lastname}</td>
</tr>
{/if}
{if $profile_fields.daytime_phone.avail}
<tr>
<td><b>{$lng.lbl_daytime_phone}:</b></td>
<td>&nbsp;</td>
<td>{$contact.daytime_phone}</td>
</tr>
{/if}
{if $is_areas.A}
<tr>
<td colspan="3"><b>{$lng.lbl_address}:</b></td>
</tr>
<tr>
<td colspan="3">
<table cellpadding="1" cellspacing="0">
{if $profile_fields.b_address.avail || $profile_fields.b_address_2.avail}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_address}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_address}<br />{$contact.b_address_2}</td>
</tr>
{/if}
{if $profile_fields.b_city.avail}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_city}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_city}</td>
</tr>
{/if}
{if $profile_fields.b_county.avail && $config.General.use_counties eq "Y"}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_county}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_countyname}</td>
</tr>
{/if}
{if $profile_fields.b_state.avail}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_state}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_statename}</td>
</tr>
{/if}
{if $profile_fields.b_country.avail}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_country}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_countryname}</td>
</tr>
{/if}
{if $profile_fields.b_zipcode.avail}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_zipcode}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_zipcode}</td>
</tr>
{/if}
</table>
</td>
</tr>
{if $profile_fields.phone.avail}
<tr>
<td><b>{$lng.lbl_phone}:</b></td>
<td>&nbsp;</td>
<td>{$contact.phone}</td>
</tr>
{/if}
{if $profile_fields.fax.avail}
<tr>
<td><b>{$lng.lbl_fax}:</b></td>
<td>&nbsp;</td>
<td>{$contact.fax}</td>
</tr>
{/if}
{if $profile_fields.email.avail}
<tr>
<td><b>{$lng.lbl_email}:</b></td>
<td>&nbsp;</td>
<td>{$contact.email}</td>
</tr>
{/if}
{if $profile_fields.url.avail}
<tr>
<td><b>{$lng.lbl_web_site}:</b></td>
<td>&nbsp;</td>
<td>{$contact.url}</td>
</tr>
{/if}
{/if}
{if $additional_fields ne ''}

{foreach from=$additional_fields item=v}
<tr>
<td><b>{$v.title}:</b></td>
<td>&nbsp;</td>
<td>{$v.value}</td>
</tr>
{/foreach}
{/if}

{if $profile_fields.department.avail}
<tr>
<td><b>{$lng.lbl_department}:</b></td>
<td>&nbsp;</td>
<td>{$contact.department}</td>
</tr>
{/if}
<tr>
<td><b>{$lng.lbl_subject}:</b></td>
<td>&nbsp;</td>
<td>{$contact.subject}</td>
</tr>
<tr>
<td colspan="3"><b>{$lng.lbl_message}:</b><br /><hr size="1" noshade="noshade" color="#DDDDDD" align="left" /></td>
</tr>
<tr>
<td colspan="3">{$contact.body}</td>
</tr>
</table>

{include file="mail/signature.tpl"}
