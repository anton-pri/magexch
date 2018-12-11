{if $mode eq 'manager_iframe'}
<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_order_id} / {$lng.lbl_date} / {$lng.lbl_status}</th>
    <th>{$lng.lbl_ship_id}</th>
    <th>{$lng.lbl_sales_manager}</th>
{if $addons.Warehouse}
    <th>{$lng.lbl_warehouse}</th>
{/if}
    <th>{$lng.lbl_customer}</th>
    <th>{$lng.lbl_city}</th>
    <th>{$lng.lbl_carrier}</th>
    <th>{$lng.lbl_tracking_id}</th>
    <th>{$lng.lbl_payment_status}</th>
</tr>
{if $ship_docs}
{foreach from=$ship_docs item=ship}
<tr align="center">
    <td>
    {if $ship.related_docs.O}
    {foreach from=$ship.related_docs.O item=order name=shr}
<a href="index.php?target=docs_O&doc_id={$order.doc_id}" target=_blank>#{$order.display_id}</a> / {$order.date|date_format:$config.Appearance.datetime_format} / {include file='main/label/doc_status.tpl' value=$order.status}
{if !$smarty.foreach.shr.last}<br/>{/if}
    {/foreach}
    {/if}
    </td>
    <td title="{foreach from=$ship.products item=product}{$product.productcode|escape} {$product.product|escape}
    {/foreach}"><a href="index.php?target=ship_docs&doc_id={$ship.doc_id}" target=_blank>#{$ship.display_id}</a></td>
    <td title="{$ship.salesman_title|escape}">{if $ship.info.salesman_customer_id}#{$ship.info.salesman_customer_id}{else}{$lng.lbl_none}{/if}</td>
{if $addons.Warehouse}
    <td title="{$ship.warehouse_title|escape}">{if $ship.info.warehouse_customer_id}#{$ship.info.warehouse_customer_id}{else}{$lng.lbl_none}{/if}</td>
{/if}
    <td title="{$ship.userinfo.main_address.firstname|escape} {$ship.userinfo.main_address.lastname|escape} {$ship.userinfo.main_address.ssn|escape}">#{$ship.userinfo.customer_id}</td>
    <td>{$ship.userinfo.main_address.city} {$ship.userinfo.main_address.state} {$ship.userinfo.main_address.country}</td>
    <td>{$ship.carrier.carrier}</td>
    <td>{$ship.tnt.shipment_key}</td>
    <td title="{$ship.cod_type_label}">{if $ship.cod_leaving_type}{$lng.lbl_ok}{else}{$lng.lbl_cod}{/if}</td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="12" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>
{else}
<script language="javascript">
function cw_shippment_search() {ldelim}
    fromdate = document.getElementById('shippment_fromdate').value;
    todate = document.getElementById('shippment_todate').value;
    shippment_search = document.getElementById('shippment_search').value;
    document.getElementById('shippment_frame').src="index.php?target={$current_target}&mode=manager_iframe&shippment_search="+shippment_search+"&from_date="+fromdate+"&to_date="+todate;
{rdelim}
</script>

<input type="hidden" id="shippment_search" value="1">
<span class="input_field_easy_0_0">
    <label>{$lng.lbl_today_shipping}</label>
    <input type="radio" name="st" value="1" onclick="javascript: document.getElementById('shippment_search').value=1"/>
    <label>{$lng.lbl_yesterday_shipping}</label>
    <input type="radio" name="st" value="2" onclick="javascript: document.getElementById('shippment_search').value=2"/>
    <label>{$lng.lbl_week_shipping}</label>
    <input type="radio" name="st" value="3" onclick="javascript: document.getElementById('shippment_search').value=3"/>
    <label>{$lng.lbl_custom_date_shipping}</label>
    <input type="radio" name="st" value="4" onclick="javascript: document.getElementById('shippment_search').value=4"/>
    {include file='main/select/date.tpl' name='shippment_fromdate' value=$search_prefilled.admin.creation_date_start} -
    {include file='main/select/date.tpl' name='shippment_todate' value=$search_prefilled.admin.creation_date_start}
</span>
{include file='buttons/button.tpl' button_title=$lng.lbl_search href="javascript:cw_shippment_search(`$user`)"}

<iframe width="100%" id="shippment_frame" height="250" src="index.php?target={$current_target}&mode=manager_iframe"></iframe>
{/if}
