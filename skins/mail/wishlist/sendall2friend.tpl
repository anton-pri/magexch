{include file='mail/mail_header.tpl'}

<p>{$lng.eml_hello}

<p>{$lng.eml_wish_list_send_msg|substitute:"sender":"`$userinfo.firstname` `$userinfo.lastname`"}

<p>
{section name=num loop=$wl_products}
<hr noshade="noshade" size="1" width="70%" align="left" />
<font color="#AA0000"><b>{$wl_products[num].product}</b></font>
<table cellpadding="0" cellspacing="0"><tr><td>
{$wl_products[num].descr|truncate:200:"..."}
</td></tr></table>
<b>{$lng.lbl_price}: <font color="#00AA00">{include file='common/currency.tpl' value=$wl_products[num].taxed_price|default:$wl_products[num].price}</font></b>
{/section}

<hr />

<p>{$lng.eml_click_to_view_wishlist}:
<br />
<a href="{$catalogs.customer}/index.php?target=gifts&mode=friends&wlid={$wlid}">{$catalogs.customer}/index.php?target=gifts&mode=friends&wlid={$wlid}</a>
</p>

{include file='mail/signature.tpl'}
