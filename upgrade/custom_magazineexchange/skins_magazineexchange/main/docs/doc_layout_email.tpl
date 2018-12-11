<p />
<table cellspacing="0" cellpadding="0" width="600" bgcolor="#ffffff">
<tr><td>
  <table width="600" height="51" border="0" cellspacing="0" cellpadding="0" style="background-image: url({$current_location}/skins/images/MagEx_Email_logo.gif)" alt="" /></td>
    <tr>
      <td width="65%">  </td>
      <td width="35%"><font style="FONT-SIZE: 28px"><b style="text-transform: uppercase;">{$lng.lbl_invoice}</b></font></td>
         </tr>
</table>
<tr>
	<td>
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td valign="top"> </td>
		<td width="100%">
		<table cellspacing="0" cellpadding="2" width="100%">
		<tr>
			<td width="30"> </td>
			<td valign="top">
<br /><br />
<b>{$lng.lbl_date}:</b>&nbsp;{$doc.date|date_format:$config.Appearance.datetime_format}<br />
<b>{$lng.lbl_order_number|default:'Order Number'}:</b>&nbsp;#{$doc.display_id}<br />
<b>{$lng.lbl_invoice_status}:</b>&nbsp;{include file="main/select/doc_status.tpl" status=$doc.status mode="static"}<br />
<b>{$lng.lbl_payment_method}:</b><br />
{$info.payment_label}<br />
<b>{$lng.lbl_delivery_method|default:'Delivery Method'}:</b><br />
{$info.shipping_label|default:'<i>unknown</i>'}
			</td>
			<td valign="bottom" align="right">
{assign var='company' value=$config.company}
<b>{$company.company_name}</b><br />
<!-- <b>Magazine Exchange</b><br />--!>
{$company.address},&nbsp;{$company.city}<br />
{$company.zipcode},&nbsp;{$company.state_name}<br />
{$company.country_name}<br />
{$lng.lbl_call_us}:&nbsp;{$company.company_phone}<br />
{$lng.lbl_international}:&nbsp;{$company.company_fax}<br />
{$lng.lbl_email}:&nbsp;{$company.site_administrator}<br /></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td><img height="2" src="{$ImagesDir}/spacer.gif" alt="" /></td>
	</tr>
	<tr>
		<td bgcolor="#58595b" style="font-size:1px"><img height="2" src="{$ImagesDir}/spacer.gif" width="100%" alt="" /></td>
	</tr>
	<tr>
		<td><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
	</tr>
	</table>
	<br />
	<table cellspacing="0" cellpadding="0" width="45%" border="0">
<tr valign="top"> 
	<td><b>{$lng.lbl_name}:</b>&nbsp;</td>
	<td>{$userinfo.main_address.title} {$userinfo.main_address.firstname} {$userinfo.main_address.lastname}</td>
</tr>
{if $userinfo.company}
<tr valign="top">
        <td><b>{$lng.lbl_company}:&nbsp;</b></td>
        <td>{$userinfo.company}</td>
</tr>
{/if}

	<tr>
		<td><b>{$lng.lbl_phone}:</b>&nbsp;</td>
		<td>{$userinfo.main_address.phone}</td>
	</tr>
	<tr>
		<td><b>{$lng.lbl_email}:</b>&nbsp;</td>
		<td>{$userinfo.email}</td>
	</tr>
	</table>
	<br />
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td width="45%" height="25"><b>{$lng.lbl_billing_address}</b></td>
		<td width="10%"> </td>
		<td width="45%" height="25"><b>{$lng.lbl_shipping_address}</b></td>
	</tr>
	<tr>
		<td bgcolor="#58595b" style="font-size:1px" height="2"><img height="2" src="{$ImagesDir}/spacer.gif" width="100%" alt="" /></td>
		<td style="font-size:1px"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
		<td bgcolor="#58595b" style="font-size:1px" height="2"><img height="2" src="{$ImagesDir}/spacer.gif" width="100%" alt="" /></td>
	</tr>
	<tr>
		<td colspan="3"><img height="2" src="{$ImagesDir}/spacer.gif" width="1" alt="" /></td>
	</tr>
	<tr>
		<td>
{assign var='address' value=$userinfo.main_address}
		<table cellspacing="0" cellpadding="0" width="100%" border="0">

{if $profile_fields.address.address.is_avail && $address.address}
		<tr>
			<td nowrap><b>{$lng.lbl_address}:</b>&nbsp;</td>
			<td>{$address.address}<br /></td>
		</tr>
{/if}
{if $profile_fields.address.address_2.is_avail && $address.address_2}
		<tr>
			<td><!--<b>Address (line 3):</b>--> </td>
			<td>{$address.address_2}</td>
		</tr>
{/if}
{if $profile_fields.address.city.is_avail && $address.city}
                <tr>
                        <td nowrap><b>{$lng.lbl_city}:</b>&nbsp;</td>
                        <td>{$address.city}</td>
                </tr>
{/if}
{if $profile_fields.address.state.is_avail && $address.statename}
                <tr>
                        <td><b>{$lng.lbl_state}:</b>&nbsp;</td>
                        <td>{$address.statename}</td>
                </tr>
{/if}
{if $profile_fields.address.zipcode.is_avail}
		<tr>
			<td><b>{$lng.lbl_post_zip_code|default:'Post/Zip Code'}:</b>&nbsp;</td>
			<td>{$address.zipcode}</td>
		</tr>
{/if}
{if $profile_fields.address.country.is_avail}
		<tr>
			<td><b>{$lng.lbl_country}:</b>&nbsp;</td>
			<td>{$address.countryname}</td>
		</tr>
{/if}
		</table>
		</td>
		<td>&nbsp;&nbsp;</td>
		<td>
{assign var='address' value=$userinfo.current_address}
		<table cellspacing="0" cellpadding="0" width="100%" border="0">
<tr valign="top"> 
        <td><b>{$lng.lbl_name}:</b>&nbsp;</td>
{if $address.firstname || $address.lastname}
        <td>{$address.title}{if $address.firstname}&nbsp;{$address.firstname}{/if}{if $address.lastname}&nbsp;{$address.lastname}{/if}</td>
{else}
        <td>{$userinfo.main_address.title}&nbsp;{$userinfo.main_address.firstname}&nbsp;{$userinfo.main_address.lastname}</td>
{/if}
</tr>

{if $profile_fields.address.address.is_avail && $address.address}
                <tr>
                        <td nowrap><b>{$lng.lbl_address}:</b>&nbsp;</td>
                        <td>{$address.address}<br /></td>
                </tr>
{/if}
{if $profile_fields.address.address_2.is_avail && $address.address_2}
                <tr>
                        <td><!--<b>Address (line 3):</b>--> </td>
                        <td>{$address.address_2}</td>
                </tr>
{/if}
{if $profile_fields.address.city.is_avail && $address.city}
                <tr>
                        <td nowrap><b>{$lng.lbl_city}:</b>&nbsp;</td>
                        <td>{$address.city}</td>
                </tr>
{/if}
{if $profile_fields.address.state.is_avail && $address.statename}
                <tr>
                        <td><b>{$lng.lbl_state}:</b>&nbsp;</td>
                        <td>{$address.statename}</td>
                </tr>
{/if}
{if $profile_fields.address.zipcode.is_avail}
                <tr>
                        <td><b>{$lng.lbl_post_zip_code|default:'Post/Zip Code'}:</b>&nbsp;</td>
                        <td>{$address.zipcode}</td>
                </tr>
{/if}
{if $profile_fields.address.country.is_avail}
                <tr>
                        <td><b>{$lng.lbl_country}:</b>&nbsp;</td>
                        <td>{$address.countryname}</td>
                </tr>
{/if}
		</table>
        </td>
	</tr>

	</table>
	<br />
	<br />

{if $usertype_layout ne 'V'}
    {include file='addons/custom_magazineexchange_sellers/main/orders/seller_info.tpl'}
    <br>
{else}
    <table cellspacing="0" cellpadding="0" width="100%" border="0">

    <tr>
    <td align="center"><font style="FONT-SIZE: 14px; FONT-WEIGHT: bold;">{$lng.lbl_products_ordered}</font></td>
    </tr>

    </table>
{/if}

<table cellspacing="0" cellpadding="3" width="100%" border="1">

<tr>
<th bgcolor="#cccccc" align="center">{$lng.lbl_item}</th>
<th nowrap="nowrap" width="100" bgcolor="#cccccc" align="center">{$lng.lbl_item_price}</th>
<th width="60" bgcolor="#cccccc" align="center">{$lng.lbl_quantity}</th>
<th width="60" bgcolor="#cccccc" align="center">{$lng.lbl_product_total|default:'Total'}<br /><img height="1" src="{$ImagesDir}/spacer.gif" width="50" border="0" alt="" /></th>
</tr>
{if $products}
{foreach from=$products item=product}
<tr>
<td><font style="FONT-SIZE: 11px">{$product.product}</font>
{include file='addons/custom_magazineexchange_sellers/main/download_link.tpl' product=$product userinfo=$userinfo status=$doc.status}
</td>
<td align="right" nowrap="nowrap"><span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$product.display_price}</span>  </td>
<td align="center">{$product.amount}</td>
<td align="right" nowrap="nowrap"><span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$product.display_subtotal}</span>  </td>
</tr>
{/foreach}
{/if}
</table>
<table cellspacing="0" cellpadding="0" width="100%" border="0">
<tr>
<td align="right" width="100%" height="20"><b>{$lng.lbl_subtotal}:&nbsp;</b> </td>
<td align="right"><span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$info.display_subtotal}</span>   </td>
</tr>
<tr>
<td align="right" height="20"><b>{$lng.lbl_shipping_cost}:&nbsp;</b> </td>
<td align="right"><span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$info.display_shipping_cost}</span></td>
</tr>
<tr>
<td bgcolor="#58595b" style="font-size:1px" colspan="2"><img height="2" src="{$ImagesDir}/spacer.gif" width="100%" alt="" /></td>
</tr>
<tr>
<td align="right" bgcolor="#cccccc" height="25"><b>{$lng.lbl_order_total|default:'Total'}:</b> </td>
<td align="right" bgcolor="#cccccc"><b><span style="WHITE-SPACE: nowrap">{include file='common/currency.tpl' value=$info.total}</span></b>   </td>
</tr>
</table>
	</td>
</tr>
<tr>
<td align="center"><br /><br /><font style="FONT-SIZE:12px">{$lng.txt_thank_you_for_purchase}</font></td>
</tr>

</table>
