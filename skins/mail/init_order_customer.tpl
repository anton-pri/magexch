{include file="mail/mail_header.tpl"}
<p />{$lng.eml_dear|substitute:"customer":"`$order.title` `$order.firstname` `$order.lastname`"},

<p />{$lng.eml_init_order_customer}

<p />{$lng.lbl_order_details_label}:

<p />
<table cellpadding="2" cellspacing="1">
<tr>
<td width="20%"><b>{$lng.lbl_order_id}:</b></td>
<td width="10">&nbsp;</td>
<td>#{$order.display_id}</td>
</tr>
<tr>
<td><b>{$lng.lbl_order_date}:</b></td>
<td>&nbsp;</td>
<td>{$order.date|date_format:$config.Appearance.datetime_format}</td>
</tr>
<tr>
<td><b>{$lng.lbl_order_status}:</b></td>
<td>&nbsp;</td>
<td>{include file="main/select/doc_status.tpl" mode="static" status=$order.status}</td>
</tr>
</table>

{include file="mail/signature.tpl"}
