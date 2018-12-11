

{$lng.eml_giftreg_notification}

{section name=num loop=$wl_products}
<p />===========================================

<p />{$wl_products[num].product}

<p />{$wl_products[num].descr|truncate:200:"..."}

<p />{$lng.lbl_price}: {include file='common/currency.tpl' value=$wl_products[num].price}

{/section}
<p />===========================================

<p />{$lng.eml_giftreg_click_to_view}:

<p /><a href="index.php?target=giftregs&eventid={$eventid}&wlid={$wlid}">{$catalogs.customer}/giftregs.php?eventid={$eventid}&wlid={$wlid}</a>

{include file="mail/signature.tpl"}
