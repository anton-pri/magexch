{if empty($seller_info)}
{tunnel func='cw_seller_get_info' via='cw_call' param1=$doc.info.warehouse_customer_id assign='seller_info'}
{/if}
{capture assign='seller_info_title'}{$lng.lbl_items_purchased_me|substitute:'seller_name':$seller_info.fullname}{/capture}
{if $is_email_invoice ne 'Y'}
<span class='items_purchased_me'>{$seller_info_title}</span>
{else}
<table cellspacing="0" cellpadding="0" width="100%" border="0">
<tr>
<td align="center"><font style="FONT-SIZE: 14px; FONT-WEIGHT: bold;">{$seller_info_title}</font></td>
</tr>
</table>
{/if}
<table style="BACKGROUND-COLOR: #C0C0C0; WIDTH: 100%;">
<tr valign='top'>
<td width='200' style="vertical-align:top">
    <i>Seller Contact Details:</i>
</td>
<td style="vertical-align:top">

{if $is_email_invoice ne 'Y'}
    {if $current_area ne 'C'}<a href='index.php?target=user_V&mode=modify&user={$doc.info.warehouse_customer_id}'>{/if}{$seller_info.name} {*$doc.info.warehouse_customer_id*}{if $current_area ne 'C'}</a>{/if}
    <br />
{/if}
    {$seller_info.fullname}
    <br />
    {$seller_info.address.address}
	{if $seller_info.address.address_2}<br> {$seller_info.address.address_2}{/if}<br />
	{$seller_info.address.city}<br />
	{$seller_info.address.zipcode}<br />
	{$seller_info.address.country}<br />
	<!--{$seller_info.address.phone}<br />-->
{if ($usertype_layout eq 'A' || $usertype_layout eq 'V') && $current_area ne 'C'}
{if $is_email_invoice eq 'Y'}</td><td class='seller_info_email' style="vertical-align:top">{/if}
    {$seller_info.email}
{/if}
</td>
</tr>
</table>
