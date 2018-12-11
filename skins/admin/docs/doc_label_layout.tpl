{* $Id: order_labels_print.tpl,v 1 2014/02/26 21:50:25 tekton Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
{include file="meta.tpl"}
</head>
<body{$reading_direction_tag}>

{assign var='info' value=$doc.info}
{assign var='userinfo' value=$doc.userinfo}
{assign var='profile_fields' value=$doc.userinfo.profile_fields}
{assign var='products' value=$doc.products}
{assign var='giftcerts' value=$doc.giftcerts}
{assign var='company' value=$doc.company_info}
{assign var='bank' value=$doc.bank_info}
{assign var='warehouse' value=$doc.warehouse_info}

<style>
{literal}
<!--
.main-table {
	border: 4px solid #000;
	border-collapse: collapse;
	font-family: Arial, Tahoma;
	width: 842px;
	height: 580px;
}
.main-table .logo {
	padding-left: 25px;
}
.main-table .empty-space {
	width: 100%;
}
.main-table .logo-links {
	padding-right: 25px;
}
.central-table {
	border: 4px solid #000;
	border-width: 4px 0;
}
.inner-central-table td{
	border: 2px solid #000;
}
.customer-address {
	font-size: +150%;
	font-weight: bold;
	padding: 1px 15px;
}
.return-address-table td {
	padding: 2px 15px;
}
//-->
{/literal}
</style>
<table cellpadding="0" cellspacing="0" border="0" class="main-table">
<tr height="60">
	<td class="logo"><img src="{$ImagesDir}/address_label_logo.jpg" width="350" /></td>
	<td class="empty-space"></td>
	<td class="logo-links"><img src="{$ImagesDir}/address_label_links.jpg" width="250" /></td>
</tr>
<tr>
	<td colspan="3" height="480" class="central-part">
		<table cellpadding="90" cellspacing="0" border="0" width="100%" height="100%" class="central-table">
		<tr>
			<td>
				<table cellpadding="3" cellspacing="15" border="0" width="100%" height="100%" class="inner-central-table">
				<tr height="25">
					<td class="address-label">Delivery Address:</td>
				</tr>
				<tr>
					<td class="customer-address">

						{if $userinfo.current_address.firstname ne ''}{$userinfo.current_address.firstname}{else}{$userinfo.main_address.firstname}{/if} {if $userinfo.current_address.lastname ne ''}{$userinfo.current_address.lastname}{else}{$userinfo.main_address.lastname}{/if}<br />
						{if $userinfo.current_address.address ne ''}{$userinfo.current_address.address}{else}{$userinfo.main_address.address}{/if},<br />

						{if $userinfo.current_address.address_2 ne ''}{$userinfo.current_address.address_2},<br />{elseif $userinfo.main_address.address_2 ne ""}{$userinfo.main_address.address_2},<br />{/if}


						{if $userinfo.current_address.city ne ''}{$userinfo.current_address.city},<br />{elseif $userinfo.main_address.city ne ''}{$userinfo.main_address.city},<br />{/if}<!--
						{if $userinfo.main_address.state ne ''}{assign var="state" value="Y"}{$userinfo.main_address.statename}{else}{assign var="state" value="Y"}{$userinfo.current_address.statename}{/if}


{if $userinfo.main_address.county ne ''}{assign var="county" value="Y"}{if $state eq 'Y'} / {/if}{$userinfo.main_address.countyname}{elseif $userinfo.current_address.county ne ''}{assign var="county" value="Y"}{if $state eq 'Y'} / {/if}{$userinfo.current_address.countyname}{/if}{if $state eq 'Y' or $county eq 'Y'},<br />{/if}-->
						{if $userinfo.current_address.zipcode ne ''}{$userinfo.current_address.zipcode}{else}{$userinfo.main_address.zipcode}{/if}<br />
						{if $userinfo.current_address.country ne ''}{$userinfo.current_address.countryname}{else}{$userinfo.main_address.countryname}{/if}.

					</td>
				</tr>
				<tr height="25">
					<td class="delivery-service">
						Delivery Service:
						<strong>
							{$info.shipping_label}{if $doc_shipping_time ne ''}, {$doc_shipping_time}{/if}
						</strong>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>

<tr height="40">
	<td colspan="3">
		<table cellpadding="2" cellspacing="0" border="1" width="100%" height="100%" class="return-address-table">
		<tr>
			<td width="120" nowrap="nowrap">Return Address: </td>
			<td>
{if empty($seller_info)}
{tunnel func='cw_seller_get_info' via='cw_call' param1=$doc.info.warehouse_customer_id assign='seller_info'}
{/if}
			{if $seller_info ne ''}

				{if $seller_info.main_address.firstname ne ''}{$seller_info.main_address.firstname}{else}{$seller_info.current_address.firstname}{/if} {if $seller_info.main_address.lastname ne ''}{$seller_info.main_address.lastname}{else}{$seller_info.current_address.lastname}{/if},
				{if $seller_info.main_address.address ne ''}{$seller_info.main_address.address}{else}{$seller_info.current_address.address}{/if},{if $seller_info.main_address.address_2 ne ''}{$seller_info.main_address.address_2},{elseif $seller_info.current_address.address_2 ne ''}{$seller_info.current_address.address_2},{/if}<br />
				
				{if $seller_info.main_address.city ne ''}{$seller_info.main_address.city}{else}{$seller_info.current_address.city}{/if},
				
				{if $seller_info.main_address.state ne ''}
				  {assign var="state" value="Y"}
				  {$seller_info.main_address.statename}
				{else}
				  {assign var="state" value="Y"}
				  {$seller_info.current_address.statename}
				{/if}
				
				{if $seller_info.main_address.county ne ''}
				  {assign var="county" value="Y"}
				  {if $state eq 'Y'} / {/if}
				  {$seller_info.main_address.countyname}
				{elseif $seller_info.current_address.county ne ''}
				  {assign var="county" value="Y"}
				  {if $state eq 'Y'} / {/if}
				  {$seller_info.current_address.countyname}
				{/if}
				{if $state eq 'Y' or $county eq 'Y'},{/if}
				{if $seller_info.main_address.zipcode ne ''}{$seller_info.main_address.zipcode}{else}{$seller_info.current_address.zipcode}{/if},
				{if $seller_info.main_address.country ne ''}{$seller_info.main_address.countryname}{else}{$seller_info.current_address.countryname}{/if}.

			{/if}
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</body>
</html>
