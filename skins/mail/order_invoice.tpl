{assign var="info" value=$order.info}
{assign var="userinfo" value=$order.userinfo}
{assign var="profile_fields" value=$order.userinfo.profile_fields}
{assign var="products" value=$order.products}
{assign var="company" value=$order.company_info}

<table cellspacing="0" cellpadding="0" width="100%" bgcolor="#ffffff" border="0">
<tr>
	<td>
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
{if $order.type eq 'S'}
	<tr>
		<td valign="top" align="left" colspan="2"><font style="FONT-SIZE: 24px" {if $is_pdf}size="24"{/if}><b>{$lng.lbl_shipment_document}</b></font></td>
        <td align="right" valign="bottom"><font style="font-size: 12px; font-weight: bold" {if $is_pdf}size="3"{/if}>{$lng.lbl_warehouse_assigned_to_ship}</font></td>
    </tr>
{else}
    <tr>
        <td valign="top" align="left" colspan="2"><font style="FONT-SIZE: 24px" {if $is_pdf}size="24"{/if}><b>{$lng.lbl_order_from_customer}</b></font></td>
        <td align="right" valign="bottom"><font style="font-size: 12px; font-weight: bold" {if $is_pdf}size="3"{/if}>&nbsp;</font></td>
    </tr>
{/if}
    <tr>
        <td bgcolor="#DDDDDD" colspan="3"><img height="1" src="{$ImagesDir}/spacer.gif" alt="" /></td>
    </tr>
    <tr>
        <td colspan="3"><img height="8" src="{$ImagesDir}/spacer.gif" alt="" /></td>
    </tr>
    <tr>
        <td width="25%">
            <table cellspacing="1" cellpadding="0" width="100%" border="0">
            <tr>
                <td><b>{$company.company_name}</b></td>
            </tr>
            <tr>
                <td>{$company.address}</td>
            </tr>
            <tr>
                <td nowrap>{$company.zipcode} {$company.state_name} {$company.city}</td>
            </tr>
            <tr>
                <td><img height="6" src="{$ImagesDir}/spacer.gif" alt="" /></td>
            </tr>
            <tr>
                <td>{$lng.lbl_vat_uppercase} {$company.vat_number}</td>
            </tr>
            <tr>
                <td bgcolor="#DDDDDD"><img height="1" src="{$ImagesDir}/spacer.gif" alt="" /></td>
            </tr>
            <tr>
                <td><img height="6" src="{$ImagesDir}/spacer.gif" alt="" /></td>
            </tr>
            <tr>
                <td>{$lng.lbl_tel} {$company.company_phone}</td>
            </tr>
            <tr>
                <td>{$lng.lbl_fax}: {$company.company_fax}</td>
            </tr>
            <tr>
                <td><img height="6" src="{$ImagesDir}/spacer.gif" alt="" /></td>
            </tr>
            <tr>
                <td nowrap>{$lng.lbl_email}: {$company.company_email}</td>
            </tr>
            </table>
        </td>
        <td width="30%" align="center" valign="middle">
            {include file='main/images/webmaster_image.tpl' image='logo_invoice'}
        </td>
        <td width="45%" valign="top">
{if $order.type eq 'S' || $order.type eq 'R'}
{assign var="warehouse" value=$order.warehouse_info}
            <table cellspacing="1" cellpadding="0" width="100%" border="0">
            <tr>
                <td><b>{$warehouse.company}</b></td>
            </tr>
            <tr>
                <td><img height="4" src="{$ImagesDir}/spacer.gif" alt="" /></td>
            </tr>
            <tr>
                <td>{$warehouse.address}</td>
            </tr>
            <tr>
                <td>{$warehouse.zipcode} {$warehouse.state_name} {$warehouse.b_city}</td>
            </tr>
            <tr>
                <td><img height="6" src="{$ImagesDir}/spacer.gif" width="50%" alt="" /></td>
            </tr>
            <tr>
                <td><b>{$lng.lbl_tel}</b> {$warehouse.phone}</td>
            </tr>
            <tr>
                <td><b>{$lng.lbl_fax}:</b> {$warehouse.fax}</td>
            </tr>
            <tr>
                <td><b>{$lng.lbl_email}:</b> {$warehouse.email}</td>
            </tr>
            <tr>
                <td><b>{$lng.lbl_contact_person}:</b> {$warehouse.firstname} {$warehouse.lastname}</td>
            </tr>
            </table>
{else}
            &nbsp;
{/if}
        </td>
    </tr>
    <tr>
        <td colspan="3"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
    </tr>
    </table>
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td><img height="2" src="{$ImagesDir}/spacer.gif" alt="" /></td>
	</tr>
	<tr>
		<td bgcolor="#000000"><img height="2" src="{$ImagesDir}/spacer_black.gif" alt="" /></td>
	</tr>
    <tr>
        <td><img height="8" src="{$ImagesDir}/spacer.gif" alt="" /></td>
    </tr>
	</table>
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
    <tr>
        <td width="40%"  valign="top">
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
{if $profile_fields.customer_company.company.is_avail}
    <tr>
        <td width="50%"><b>{$lng.lbl_company}:</b></td>
        <td>{$userinfo.company}</td>
    </tr>
{/if}
{if $profile_fields.basic.tax_number.is_avail}
    <tr>
        <td><b>{if $userinfo.usertype eq 'R'}{$lng.lbl_tax_number_reseller}{else}{$lng.lbl_tax_number}{/if}:</b></td>
        <td>{$userinfo.tax_number}</td>
    </tr>
{/if}
{*if $_userinfo.profile_fields.firstname}
    <tr>
        <td nowrap="nowrap"><b>{$lng.lbl_firstname}:</b></td>
        <td>{$order.firstname}</td>
    </tr>
{/if}
{if $_userinfo.profile_fields.lastname}
    <tr>
        <td nowrap="nowrap"><b>{$lng.lbl_lastname}:</b></td>
        <td>{$order.lastname}</td>
    </tr>
{/if}
{if $_userinfo.profile_fields.phone}
    <tr>
        <td><b>{$lng.lbl_phone}:</b></td>
        <td>{$order.phone}</td>
    </tr>
{/if}
{if $_userinfo.profile_fields.fax}
    <tr>
        <td><b>{$lng.lbl_fax}:</b></td>
        <td>{$order.fax}</td>
    </tr>
{/if*}
{*if $_userinfo.profile_fields.url}
    <tr>
        <td><b>{$lng.lbl_url}:</b></td>
        <td>{$order.url}</td>
    </tr>
{/if*}
            </table>
        </td>
        <td width="5%">&nbsp;</td>
        <td width="55%" valign="top">
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
    <tr>
        <td width="50%"><b>{$lng.lbl_date}:</b></td>
        <td>{$order.date|date_format:$config.Appearance.datetime_format}</td>
    </tr>
    <tr>
        <td colspan="2"><img height="4" src="{$ImagesDir}/spacer.gif" alt="" /></td>
    </tr>
    {if $order.type eq 'S' || $order.type eq 'R'}
    <tr>
        <td><b>{$lng.lbl_shipment_document_number}:</b></td>
        <td>{$order.display_id}</td>
    </tr>
    <tr>
        <td colspan="2"><img height="4" src="{$ImagesDir}/spacer.gif" alt="" /></td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>{$lng.lbl_payment_status}:</b></td>
        <td>
            <u>{if $order.status eq "P" or $order.status eq "C"}{$lng.lbl_processed}{else}{$lng.lbl_pending}{/if}</u>
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>{$lng.lbl_forwarder_assigned}:</b></td>
        <td>{$order.forwarder}</td>
    </tr>
    {elseif $order.type eq 'I'}
    <tr>
        <td><b>{$lng.lbl_invoce_number}:</b></td>
        <td>{$order.display_id}</td>
    </tr>
    <tr>
        <td colspan="2"><img height="4" src="{$ImagesDir}/spacer.gif" alt="" /></td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>{$lng.lbl_invoice_status}:</b></td>
        <td>
            <u>{include file="main/invoice_status.tpl" extended=true}</u>
        </td>
    </tr>
    {else}
    <tr>
        <td><b>{$lng.lbl_order_id}:</b></td>
        <td>#{$order.display_id}</td>
    </tr>
    <tr>
        <td colspan="2"><img height="4" src="{$ImagesDir}/spacer.gif" alt="" /></td>
    </tr>
    <tr>
        <td nowrap="nowrap"><b>{$lng.lbl_order_status}:</b></td>
        <td><u>{include file="main/select/doc_status.tpl" status=$order.status mode="static"}</u></td>
    </tr>
    {/if}
    <tr>
        <td colspan="2"><img height="4" src="{$ImagesDir}/spacer.gif" alt="" /></td>
    </tr>
    <tr>
        <td><b>{$lng.lbl_delivery}:</b></td>
        <td>{$info.shipping_label|trademark|default:$lng.txt_not_available}</td>
    </tr>
            </table>
        </td>
    </tr>
    </table>

	<br />
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td width="40%" height="25"><b>{$lng.lbl_billing_address}</b></td>
		<td width="5%">&nbsp;</td>
		<td width="55%" height="25"><b>{$lng.lbl_shipping_address}</b></td>
	</tr>
	<tr>
		<td bgcolor="#000000" height="2"><img height="2" src="{$ImagesDir}/spacer_black.gif" alt="" /></td>
		<td><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
		<td bgcolor="#000000" height="2"><img height="2" src="{$ImagesDir}/spacer_black.gif" alt="" /></td>
	</tr>
	<tr>
		<td colspan="3"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
	</tr>
	<tr>
		<td valign="top">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
{if $profile_fields.address.firstname.is_avail}
		<tr>
			<td><b>{$lng.lbl_firstname}:</b> </td>
			<td>{$userinfo.current_address.firstname}</td>
		</tr>
{/if}
{if $profile_fields.address.lastname.is_avail}
		<tr>
			<td><b>{$lng.lbl_lastname}:</b> </td>
			<td>{$userinfo.current_address.lastname}</td>
		</tr>
{/if}
{if $profile_fields.address.address.is_avail}
		<tr>
			<td width="50%"><b>{$lng.lbl_address}:</b> </td>
			<td>{$userinfo.current_address.address}<br />{$userinfo.current_address.address_2}</td>
		</tr>
{/if}
{if $profile_fields.address.city.is_avail}
		<tr>
			<td><b>{$lng.lbl_city}:</b> </td>
			<td>{$userinfo.current_address.city}</td>
		</tr>
{/if}
{if $profile_fields.address.county.is_avail && $config.General.use_counties eq 'Y'}
		<tr>
			<td><b>{$lng.lbl_county}:</b> </td>
			<td>{$userinfo.current_address.countyname}</td>
		</tr>
{/if}
{if $profile_fields.address.state.is_avail}
		<tr>
			<td><b>{$lng.lbl_state}:</b> </td>
			<td>{$userinfo.current_address.statename}</td>
		</tr>
{/if}
{if $profile_fields.address.country.is_avail}
		<tr>
			<td><b>{$lng.lbl_country}:</b> </td>
			<td>{$userinfo.current_address.countryname}</td>
		</tr>
{/if}
{if $profile_fields.address.zipcode.is_avail}
		<tr>
			<td><b>{$lng.lbl_zipcode}:</b> </td>
			<td>{$userinfo.current_address.zipcode}</td>
		</tr>
{/if}
{if $profile_fields.address.email.is_avail}
        <tr>
            <td><b>{$lng.lbl_email}:</b> </td>
            <td>{$userinfo.current_address.email}</td>
        </tr>
{/if}
{if $profile_fields.address.fax.is_avail}
        <tr>
            <td><b>{$lng.lbl_zipcode}:</b> </td>
            <td>{$userinfo.current_address.fax}</td>
        </tr>
{/if}
{if $profile_fields.address.phone.is_avail}
        <tr>
            <td><b>{$lng.lbl_zipcode}:</b> </td>
            <td>{$userinfo.current_address.zipcode}</td>
        </tr>
{/if}
		</table>
		</td>
		<td>&nbsp;</td>
		<td valign="top">
		<table cellspacing="0" cellpadding="0" width="100%" border="0">
{if $profile_fields.address.firstname.is_avail}
		<tr>
			<td><b>{$lng.lbl_firstname}:</b> </td>
			<td>{$userinfo.main_address.firstname}</td>
		</tr>
{/if}
{if $profile_fields.address.lastname.is_avail}
		<tr>
			<td><b>{$lng.lbl_lastname}:</b> </td>
			<td>{$userinfo.main_address.lastname}</td>
		</tr>
{/if}
{if $profile_fields.address.address.is_avail}
		<tr>
			<td width="50%"><b>{$lng.lbl_address}:</b> </td>
			<td>{$userinfo.main_address.address}<br />{$userinfo.main_address.address_2}</td>
		</tr>
{/if}
{if $profile_fields.address.city.is_avail}
		<tr>
			<td><b>{$lng.lbl_city}:</b> </td>
			<td>{$userinfo.main_address.city}</td>
		</tr>
{/if}
{if $profile_fields.address.county.is_avail && $config.General.use_counties eq 'Y'}
		<tr>
			<td><b>{$lng.lbl_county}:</b> </td>
			<td>{$userinfo.main_address.countyname}</td>
		</tr>
{/if}
{if $profile_fields.address.state.is_avail}
		<tr>
			<td><b>{$lng.lbl_state}:</b> </td>
			<td>{$userinfo.main_address.statename}</td>
		</tr>
{/if}
{if $profile_fields.address.country.is_avail}
		<tr>
			<td><b>{$lng.lbl_country}:</b> </td>
			<td>{$userinfo.main_address.countryname}</td>
		</tr>
{/if}
{if $profile_fields.address.zipcode.is_avail}
		<tr>
			<td><b>{$lng.lbl_zipcode}:</b> </td>
			<td>{$userinfo.main_address.zipcode}</td>
		</tr>
{/if}
{if $profile_fields.address.email.is_avail}
        <tr>
            <td><b>{$lng.lbl_email}:</b> </td>
            <td>{$userinfo.main_address.email}</td>
        </tr>
{/if}
{if $profile_fields.address.fax.is_avail}
        <tr>
            <td><b>{$lng.lbl_fax}:</b> </td>
            <td>{$userinfo.main_address.fax}</td>
        </tr>
{/if}
{if $profile_fields.address.phone.is_avail}
        <tr>
            <td><b>{$lng.lbl_phone}:</b> </td>
            <td>{$userinfo.main_address.phone}</td>
        </tr>
{/if}
		</table>

        </td>
	</tr>
{assign var="is_header" value=""}
{foreach from=$_userinfo.additional_fields item=v}
{if $v.section eq 'A'}
{if $is_header eq ''}
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
<tr>
	<td width="45%" height="25"><b>{$lng.lbl_additional_information}</b></td>
	<td colspan="2" width="55%">&nbsp;</td>
</tr>
<tr>
	<td bgcolor="#000000" height="2"><img height="2" src="{$ImagesDir}/spacer_black.gif" alt="" /></td>
	<td colspan="2" width="55%"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
</tr>
<tr>
	<td colspan="3"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
</tr>
<tr>
	<td><table cellspacing="0" cellpadding="0" width="100%" border="0">
{assign var="is_header" value="E"}
{/if}
<tr valign="top">
	<td><b>{$v.title}</b></td>
   	<td>{$v.value}</td>
</tr>
{/if}
{/foreach}
{if $is_header eq 'E'}
</table></td>
<td colspan="2" width="55%">&nbsp;</td>
</tr>
{/if}


{if $config.Email.show_cc_info eq "Y" and $usertype_layout eq "A"}

	<tr>
	<td colspan="3">&nbsp;</td>
	</tr>

	<tr>
	<td width="45%" height="25"><b>{$lng.lbl_order_payment_details}</b></td>
	<td colspan="2" width="55%">&nbsp;</td>
	</tr>
	
	<tr>
	<td bgcolor="#000000" height="2"><img height="2" src="{$ImagesDir}/spacer_black.gif" width="100%" alt="" /></td>
	<td colspan="2"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
	</tr>
	<tr>
	<td colspan="3"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
	</tr>

	<tr>
	<td colspan="3">{$order.details|replace:"\n":"<br />"}</td>
	</tr>

{/if}

{if $order.netbanx_reference}
<tr>
	<td colspan="3">NetBanx Reference: {$order.netbanx_reference}</td>
</tr>
{/if}

	</table>
	<br />
	<br />

{if $order.type eq 'I' || $is_credit eq "Y"}
<div align="right">
<b>{$lng.lbl_reference}: {$lng.lbl_order_number} {$order.main_order_info.display_id} ({$order.main_order_info.date|date_format:$config.Appearance.date_format})&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;{$lng.lbl_shipment_document_number} {$order.ship_order_info.display_id} ({$order.ship_order_info.date|date_format:$config.Appearance.date_format})</b>
</div>
{/if}

{include file="main/orders/order_data.tpl"}

	</td>
</tr>

{if $info.customer_notes ne ""}

<tr>
	<td colspan="3">
	<br />
	<br />
	<table cellspacing="0" cellpadding="0" width="100%" border="0">

	<tr>
		<td align="center"><font style="FONT-SIZE: 14px; FONT-WEIGHT: bold;">{$lng.lbl_customer_notes}</font></td>
	</tr>

	</table>
	<table cellspacing="0" cellpadding="10" width="100%" border="0">
	<tr>
		<td style="height:50px;">{$info.customer_notes}</td>
	</tr>
	</table>
	</td>
</tr>

{/if}
{if $order.type eq 'S' || $order.type eq 'R'}
<tr>
<td align="center"><br /><br /><font style="FONT-SIZE:12px">{$lng.txt_thank_you_for_purchase}</font></td>
</tr>
{/if}
</table>

