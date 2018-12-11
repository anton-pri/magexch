<a name="orders"></a>
{capture name=section}
{$lng.txt_top_info_orders}

<br /><br />

<div align="center">
<table class="header bordered" width="100%">
<tr>
    <th>{$lng.lbl_status}</th>
    <th>{$lng.lbl_since_last_log_in}</th>
    <th>{$lng.lbl_today}</th>
    <th>{$lng.lbl_this_week}</th>
    <th>{$lng.lbl_this_month}</th>
</tr>
{foreach key=key item=item from=$orders}
<tr{cycle values=" class='cycle',"}>
    <td nowrap="nowrap" align="left">{if $key eq "P"}{$lng.lbl_processed}{elseif $key eq "Q"}{$lng.lbl_queued}{elseif $key eq "F" or $key eq "D"}{$lng.lbl_failed}/{$lng.lbl_declined}{elseif $key eq "I"}{$lng.lbl_not_finished}{/if}:</td>
{section name=period loop=$item}
    <td align="center">{$item[period]}</td>
{/section}
</tr>
{/foreach}

<tr{cycle values=" class='cycle',"}>
    <td align="right"><b>{$lng.lbl_gross_total}:</b></td>
{section name=period loop=$gross_total}
    <td align="center">{include file='common/currency.tpl' value=$gross_total[period]}</td>
{/section} 
</tr>

<tr{cycle values=" class='cycle',"}>
    <td align="right"><b>{$lng.lbl_total_paid}:</b></td>
{section name=period loop=$total_paid}
    <td align="center">{include file='common/currency.tpl' value=$total_paid[period]}</td>
{/section}
</tr>

</table>
</div>

<br /><br />

<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_search_orders href="index.php?target=orders" title=$lng.lbl_search_orders}</div>

{if $last_order}
<br /><br />

{include file="common/subheader.tpl" title=$lng.lbl_last_order}

<div class="input_field_1">
    <label>{$lng.lbl_order_id}</label>
    #{$last_order.display_id}
</div>

<div class="input_field_1">
    <label>{$lng.lbl_order_date}</label>
    {$last_order.date|date_format:$config.Appearance.datetime_format}
</div>

<div class="input_field_1">
    <label>{$lng.lbl_order_status}</label>
    {include file="main/select/doc_status.tpl" status=$last_order.status mode="static"}
</div>

<div class="input_field_1">
    <label>{lng name="lbl_user_`$last_order.userinfo.usertype`"}</label>
    {$last_order.userinfo.customer_id|user_title:$last_order.userinfo.usertype:$last_order.userinfo.doc_id}
</div>

<div class="input_field_1">
    <label>{$lng.lbl_ordered}</label>
{if $last_order.products}
{section name=product loop=$last_order.products}
<b>{$last_order.products[product].product|truncate:"30":"..."}</b>
[{$lng.lbl_price}: {include file='common/currency.tpl' value=$last_order.products[product].price}, {$lng.lbl_quantity}: {$last_order.products[product].amount}]
{if $last_order.products[product].product_options}
<br />
{$lng.lbl_options}: {$last_order.products[product].product_options|replace:"\n":"; "}
{/if}
<br />
{/section}
{/if}
{if $last_order.giftcerts}
{section name=gc loop=$last_order.giftcerts}
<b>{$lng.lbl_gift_certificate} #{$last_order.giftcerts[gc].gc_id}</b>
[{$lng.lbl_price}: {include file='common/currency.tpl' value=$last_order.giftcerts[gc].amount}]
<br />
{/section}
{/if}
</div>

<br />

<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_order_details_label href="index.php?target=order&doc_id=`$last_order.doc_id`" title=$lng.lbl_order_details_label}</div>

{/if}
{/capture}
{include file="common/section.tpl" title=$lng.lbl_orders_info content=$smarty.capture.section extra='width="100%"'}
