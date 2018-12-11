{include file="mail/mail_header.tpl"}

<p>{$lng.eml_hello}

<p>{$lng.eml_send2friend|substitute:"sender":"`$name`"}

<p>{$message}</p>
<p><font color="#AA0000"><b>{$product.product}</b></font>
<hr noshade="noshade" size="1" width="70%" align="left" />
<table cellpadding="0" cellspacing="0"><tr><td>
{$product.descr}
</td></tr></table>
<b>{$lng.lbl_price}: <font color="#00AA00">{include file='common/currency.tpl' value=$product.taxed_price|default:$product.price}</font></b>


<p>
{$lng.eml_click_to_view_product}:
<br />
<a href="{$catalogs.customer}/{pages_url var="product" product_id=$product.product_id}">{$catalogs.customer}/{pages_url var="product" product_id=$product.product_id}</a>
</p>

{include file="mail/signature.tpl"}

