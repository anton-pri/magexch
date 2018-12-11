{include file="mail/mail_header.tpl"}
<p />{$lng.eml_dear|substitute:"customer":"`$customer.title` `$customer.firstname` `$customer.lastname`"},

<p />{$lng.eml_order_declined}

<hr size="1" noshade="noshade" />
<p />
<table cellpadding="2" cellspacing="1" width="100%">
<tr>
<td width="20%"><b>{$lng.lbl_order_id}:</b></td>
<td width="10">&nbsp;</td>
<td width="80%"><tt><b>#{$order.display_id}</b></tt></td>
</tr>
<tr>
<td><b>{$lng.lbl_order_date}:</b></td>
<td width="10">&nbsp;</td>
<td><tt><b>{$order.date|date_format:$config.Appearance.datetime_format}</b></tt></td>
</tr>

<tr>
	<td colspan="3">{include file="main/orders/order_data.tpl"}</td>
</tr>
</table>

{include file="mail/signature.tpl"}
