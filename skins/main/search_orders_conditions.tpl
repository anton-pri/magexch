{include_once_src file="main/include_js.tpl" src="reset.js"}
<script type="text/javascript">
<!--
var searchform_def = [
    ['posted_data[date_period]', '{$search_prefilled.date_period}'],
    ['StartDay', '{$search_prefilled.start_date|default:$smarty.now|date_format:"%d"}'],
    ['StartMonth', '{$search_prefilled.start_date|default:$smarty.now|date_format:"%m"}'],
    ['StartYear', '{$search_prefilled.start_date|default:$smarty.now|date_format:"%Y"}'],
    ['EndDay', '{$search_prefilled.end_date|default:$smarty.now|date_format:"%d"}'],
    ['EndMonth', '{$search_prefilled.end_date|default:$smarty.now|date_format:"%m"}'],
    ['EndYear', '{$search_prefilled.end_date|default:$smarty.now|date_format:"%Y"}'],
    ['posted_data[total_min]', '{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.total_min|formatprice}{/if}'],
    ['posted_data[total_max]', '{$search_prefilled.total_max|formatprice}'],
    ['posted_data[by_title]', {if $search_prefilled eq "" or $search_prefilled.by_title}true{else}false{/if}],
    ['posted_data[by_options]', {if $search_prefilled eq "" or $search_prefilled.by_options}true{else}false{/if}],
    ['posted_data[price_min]', '{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.price_min|formatprice}{/if}'],
    ['posted_data[price_max]', '{$search_prefilled.price_max|formatprice}'],
    ['posted_data[address_type]', '{$search_prefilled.address_type}'],
    ['posted_data[display_id1]', '{$search_prefilled.display_id1}'],
    ['posted_data[display_id2]', '{$search_prefilled.display_id2}'],
    ['posted_data[main_display_id1]', '{$search_prefilled.main_display_id1}'],
    ['posted_data[main_display_id2]', '{$search_prefilled.main_display_id2}'],
    ['posted_data[payment_method]', '{$search_prefilled.payment_method}'],
    ['posted_data[product_substring]', '{$search_prefilled.product_substring|escape:javascript}'],
    ['posted_data[features][]', '{foreach from=$search_prefilled.features item=fv key=fk}{$fk},{/foreach}'],
    ['posted_data[warehouse]', '{$search_prefilled.warehouse}'],
    ['posted_data[shipping_method]', '{$search_prefilled.shipping_method}'],
    ['posted_data[productcode]', '{$search_prefilled.productcode|escape:javascript}'],
    ['posted_data[product_id]', '{$search_prefilled.product_id|escape:javascript}'],
    ['posted_data[customer]', '{$search_prefilled.customer|escape:javascript}'],
    ['posted_data[by_username]', {if $search_prefilled eq "" or $search_prefilled.by_username}true{else}false{/if}],
    ['posted_data[by_firstname]', {if $search_prefilled eq "" or $search_prefilled.by_firstname}true{else}false{/if}],
    ['posted_data[by_lastname]', {if $search_prefilled eq "" or $search_prefilled.by_lastname}true{else}false{/if}],
    ['posted_data[city]', '{$search_prefilled.city|escape:javascript}'],
    ['posted_data[state]', '{$search_prefilled.state|escape:javascript}'],
    ['posted_data[country]', '{$search_prefilled.country|escape:javascript}'],
    ['posted_data[zipcode]', '{$search_prefilled.zipcode|escape:javascript}'],
    ['posted_data[phone]', '{$search_prefilled.phone|escape:javascript}'],
    ['posted_data[email]', '{$search_prefilled.email|escape:javascript}'],
    ['posted_data[status]', '{$search_prefilled.status}']
];
{literal}
function managedate(type, status) {
    if (type != 'date')
        var fields = ['posted_data[city]','posted_data[state]','posted_data[country]','posted_data[zipcode]'];
    else
        var fields = ['StartDay','StartMonth','StartYear','EndDay','EndMonth','EndYear'];

    for (i in fields)
        if (document.searchform.elements[fields[i]])
            document.searchform.elements[fields[i]].disabled = status;
}
{/literal}
-->
</script>

{capture name=section}

<form name="searchform" action="index.php?target={$current_script}" method="post">
<input type="hidden" name="action" value="" />

<table cellpadding="0" cellspacing="0" width="100%">

<tr>
    <td>

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
    <td colspan="3">
{$lng.txt_search_orders_text}
<br /><br />
    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_date_period}:</td>
    <td width="10">&nbsp;</td>
    <td>
<table cellpadding="0" cellspacing="0">
<tr>
    <td width="5"><input type="radio" id="date_period_null" name="posted_data[date_period]" value=""{if $search_prefilled eq "" or $search_prefilled.date_period eq ""} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
    <td class="OptionLabel"><label for="date_period_null">{$lng.lbl_all_dates}</label></td>

    <td width="5"><input type="radio" id="date_period_M" name="posted_data[date_period]" value="M"{if $search_prefilled.date_period eq "M"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
    <td class="OptionLabel"><label for="date_period_M">{$lng.lbl_this_month}</label></td>

    <td width="5"><input type="radio" id="date_period_W" name="posted_data[date_period]" value="W"{if $search_prefilled.date_period eq "W"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
    <td class="OptionLabel"><label for="date_period_W">{$lng.lbl_this_week}</label></td>

    <td width="5"><input type="radio" id="date_period_D" name="posted_data[date_period]" value="D"{if $search_prefilled.date_period eq "D"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
    <td class="OptionLabel"><label for="date_period_D">{$lng.lbl_today}</label></td>
</tr>
<tr>
    <td width="5"><input type="radio" id="date_period_C" name="posted_data[date_period]" value="C"{if $search_prefilled.date_period eq "C"} checked="checked"{/if} onclick="javascript:managedate('date',false)" /></td>
    <td colspan="7" class="OptionLabel"><label for="date_period_C">{$lng.lbl_specify_period_below}</label></td>
</tr>
</table>
</td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_order_date_from}:</td>
    <td width="10">&nbsp;</td>
    <td>
    {html_select_date prefix="Start" time=$search_prefilled.start_date start_year=$config.Company.start_year end_year=$config.Company.end_year}
    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_order_date_through}:</td>
    <td width="10">&nbsp;</td>
    <td>
    {html_select_date prefix="End" time=$search_prefilled.end_date start_year=$config.Company.start_year end_year=$config.Company.end_year display_days=yes}
    </td>
</tr>

{if $subsalesmans}
<tr>
    <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_sales_manager}:</td>
    <td width="10" height="10"><font class="CustomerMessage"></font></td>
    <td height="10" width="80%">
    <select name="posted_data[subsalesman]">
    <option value="">{$lng.lbl_myself}</option>
    {foreach from=$subsalesmans item=subsalesman}
    <option value="{$subsalesman.customer_id}" {if $search_prefilled.subsalesman eq $subsalesman.customer_id}selected{/if}>{$subsalesman.firstname} {$subsalesman.lastname}</option>
    {/foreach}
    </select>
    &nbsp;
    </td>
</tr>
{/if}

<tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="3" class="SubmitBox">
    <input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" onclick="javascript: document.searchform.mode.value=''; document.searchform.submit();" />

{if $search_prefilled.date_period ne "C"}
<script type="text/javascript" language="JavaScript 1.2">
<!--
managedate('date',true);
-->
</script>
{/if}
    </td>
</tr>

</table>

<br />

{include file="main/visiblebox_link.tpl" mark="1" title=$lng.lbl_advanced_search_options}

<br />

<table cellpadding="1" cellspacing="5" width="100%" style="display: none;" id="box1">

<tr>
    <td colspan="3"><br />{include file="common/subheader.tpl" title=$lng.lbl_advanced_search_options}</td>
</tr>

<tr>
    <td colspan="3">{$lng.txt_adv_search_orders_text}<br /><br /></td>
</tr>

{if $usertype eq 'A'}
<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_company}:</td>
    <td width="10">&nbsp;</td>
    <td>
    <select name="posted_data[company_id]" style="width:70%">
        <option value=""></option>
{foreach from=$companies item=company}
        <option value="{$company.company_id}"{if $search_prefilled.company_id eq $company.company_id} selected="selected"{/if}>{$company.company_name}</option>
{/foreach}
    </select>
    </td>
</tr>
{/if}

<tr>
    <td width="25%" class="FormButton" nowrap="nowrap">{if $current_script eq 'invoices'}{$lng.lbl_invoice_id}{else}{$lng.lbl_ship_doc_id}{/if}:</td>
    <td width="10">&nbsp;</td>
    <td width="75%">
<input type="text" name="posted_data[display_id1]" size="10" maxlength="15" value="{$search_prefilled.display_id1}" />
-
<input type="text" name="posted_data[display_id2]" size="10" maxlength="15"value="{$search_prefilled.display_id2}" />
    </td>
</tr>

<tr>
    <td width="25%" class="FormButton" nowrap="nowrap">{$lng.lbl_order_id}:</td>
    <td width="10">&nbsp;</td>
    <td width="75%">
<input type="text" name="posted_data[main_display_id1]" size="10" maxlength="15" value="{$search_prefilled.main_display_id1}" />
-
<input type="text" name="posted_data[main_display_id1]" size="10" maxlength="15"value="{$search_prefilled.main_display_id2}" />
    </td>
</tr>

{if $usertype ne "C"}
<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_order_total} ({$config.General.currency_symbol}):</td>
    <td width="10">&nbsp;</td>
    <td>

<table cellpadding="0" cellspacing="0">
<tr>
    <td><input type="text" size="10" maxlength="15" name="posted_data[total_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.total_min|formatprice}{/if}" /></td>
    <td>&nbsp;-&nbsp;</td>
    <td><input type="text" size="10" maxlength="15" name="posted_data[total_max]" value="{$search_prefilled.total_max|formatprice}" /></td>
</tr>
</table>

    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_payment_method}:</td>
    <td width="10">&nbsp;</td>
    <td>
    <select name="posted_data[payment_method]" style="width:70%">
        <option value=""></option>
{section name=pm loop=$payment_methods}
        <option value="{$payment_methods[pm].payment_method}"{if $search_prefilled.payment_method eq $payment_methods[pm].payment_method} selected="selected"{/if}>{$payment_methods[pm].payment_method}</option>
{/section}
    </select>
    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_delivery}:</td>
    <td width="10">&nbsp;</td>
    <td>
    <select name="posted_data[shipping_method]" style="width:70%">
        <option value=""></option>
{section name=sm loop=$shipping_methods}
        <option value="{$shipping_methods[sm].shipping_id}"{if $search_prefilled.shipping_method eq $shipping_methods[sm].shipping_id} selected="selected"{/if}>{$shipping_methods[sm].shipping|trademark}</option>
{/section}
    </select>
    </td>
</tr>

{/if}

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_order_status}:</td>
    <td width="10">&nbsp;</td>
    <td>{include file="main/select/doc_status.tpl" status=$search_prefilled.status mode="select" name="posted_data[status]" extended="Y" extra="style='width:70%'"}</td>
</tr>

{if $usertype ne "C"}
{if $usertype eq "A"}
<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_warehouse}:</td>
    <td width="10">&nbsp;</td>
    <td>
    <input type="text" name="posted_data[warehouse]" size="30" value="{$search_prefilled.warehouse}" style="width:70%" />
    </td>
</tr>
{/if}

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_order_features}:</td>
    <td width="10">&nbsp;</td>
    <td>
{assign var="features" value=$search_prefilled.features}
    <select name="posted_data[features][]" multiple="multiple" size="7" style="width:70%">
        <option value="gc_applied"{if $features.gc_applied} selected="selected"{/if}>{$lng.lbl_entirely_or_partially_payed_by_gc|strip_tags}</option>
        <option value="discount_applied"{if $features.discount_applied} selected="selected"{/if}>{$lng.lbl_global_discount_applied|strip_tags}</option>
        <option value="coupon_applied"{if $features.coupon_applied} selected="selected"{/if}>{$lng.lbl_discount_coupon_applied|strip_tags}</option>
        <option value="free_ship"{if $features.free_ship} selected="selected"{/if}>{$lng.lbl_free_shipping|strip_tags}</option>
        <option value="free_tax"{if $features.free_tax} selected="selected"{/if}>{$lng.lbl_tax_exempt|strip_tags}</option>
        <option value="gc_ordered"{if $features.gc_ordered} selected="selected"{/if}>{$lng.lbl_gc_purchased|strip_tags}</option>
        <option value="notes"{if $features.notes} selected="selected"{/if}>{$lng.lbl_orders_with_notes_assigned|strip_tags}</option>
    </select><br />
{$lng.lbl_hold_ctrl_key}
    </td>
</tr>

{/if}

{if $usertype ne "C"}

<tr>
    <td colspan="3"><br />{include file="common/subheader.tpl" title=$lng.lbl_search_by_ordered_products class="grey"}</td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_pattern}:</td>
    <td width="10"><font class="CustomerMessage"></font></td>
    <td>
    <input type="text" name="posted_data[product_substring]" size="30" value="{$search_prefilled.product_substring}" style="width:70%" />
    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_search_in}:</td>
    <td width="10"><font class="CustomerMessage"></font></td>
    <td>

<table cellpadding="0" cellspacing="0">
<tr>
    <td width="5"><input type="checkbox" id="posted_data_by_title" name="posted_data[by_title]"{if $search_prefilled eq "" or $search_prefilled.by_title} checked="checked"{/if} /></td>
    <td nowrap="nowrap"><label for="posted_data_by_title">{$lng.lbl_product_title}</label>&nbsp;&nbsp;</td>

    <td width="5"><input type="checkbox" id="posted_data_by_options" name="posted_data[by_options]"{if $search_prefilled eq "" or $search_prefilled.by_options} checked="checked"{/if} /></td>
    <td nowrap="nowrap"><label for="posted_data_by_options">{$lng.lbl_options}</label></td>
</tr>
</table>

    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_sku}:</td>
    <td width="10"><font class="CustomerMessage"></font></td>
    <td>
    <input type="text" maxlength="64" name="posted_data[productcode]" value="{$search_prefilled.productcode}" style="width:70%" />
    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_product_id}#:</td>
    <td width="10"><font class="CustomerMessage"></font></td>
    <td>
    <input type="text" maxlength="64" name="posted_data[product_id]" value="{$search_prefilled.product_id}" style="width:70%" />
    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_price} ({$config.General.currency_symbol}):</td>
    <td width="10">&nbsp;</td>
    <td>
<table cellpadding="0" cellspacing="0">
<tr>
    <td><input type="text" size="10" class="input_small" maxlength="15" name="posted_data[price_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.price_min|formatprice}{/if}" /></td>
    <td>&nbsp;-&nbsp;</td>
    <td><input type="text" size="10" class="input_small" maxlength="15" name="posted_data[price_max]" value="{$search_prefilled.price_max|formatprice}" /></td>
</tr>
</table>
    </td>
</tr>

{/if}

{if $usertype ne "C"}

<tr>
    <td colspan="3"><br />{include file="common/subheader.tpl" title=$lng.lbl_search_by_customer class="grey"}</td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_customer}:</td>
    <td width="10">&nbsp;</td>
    <td><input type="text" name="posted_data[customer]" size="30" value="{$search_prefilled.customer}" style="width:70%" /></td>
</tr>

<tr>
    <td class="FormButton">{$lng.lbl_search_in}:</td>
    <td width="10">&nbsp;</td>
    <td>
<table cellspacing="0" cellpadding="0">
<tr>
    <td width="5"><input type="checkbox" id="posted_data_by_username" name="posted_data[by_username]"{if $search_prefilled eq "" or $search_prefilled.by_username} checked="checked"{/if} /></td>
    <td nowrap="nowrap"><label for="posted_data_by_username">{$lng.lbl_username}</label>&nbsp;&nbsp;</td>

    <td width="5"><input type="checkbox" id="posted_data_by_firstname" name="posted_data[by_firstname]"{if $search_prefilled eq "" or $search_prefilled.by_firstname} checked="checked"{/if} /></td>
    <td nowrap="nowrap"><label for="posted_data_by_firstname">{$lng.lbl_firstname}</label>&nbsp;&nbsp;</td>

    <td width="5"><input type="checkbox" id="posted_data_by_lastname" name="posted_data[by_lastname]"{if $search_prefilled eq "" or $search_prefilled.by_lastname} checked="checked"{/if} /></td>
    <td nowrap="nowrap"><label for="posted_data_by_lastname">{$lng.lbl_lastname}</label></td>
</tr>
</table>
    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_search_by_address}:</td>
    <td width="10"><font class="CustomerMessage"></font></td>
    <td>
<table cellpadding="0" cellspacing="0">
<tr>
    <td width="5"><input type="radio" id="address_type_null" name="posted_data[address_type]" value=""{if $search_prefilled eq "" or $search_prefilled.address_type eq ""} checked="checked"{/if} onclick="javascript:managedate('address',true)" /></td>
    <td class="OptionLabel"><label for="address_type_null">{$lng.lbl_ignore_address}</label></td>

    <td width="5"><input type="radio" id="address_type_B" name="posted_data[address_type]" value="B"{if $search_prefilled.address_type eq "B"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
    <td class="OptionLabel"><label for="address_type_B">{$lng.lbl_billing}</label></td>

    <td width="5"><input type="radio" id="address_type_S" name="posted_data[address_type]" value="S"{if $search_prefilled.address_type eq "S"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
    <td class="OptionLabel"><label for="address_type_S">{$lng.lbl_shipping}</label></td>

    <td width="5"><input type="radio" id="address_type_both" name="posted_data[address_type]" value="Both"{if $search_prefilled.address_type eq "Both"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
    <td class="OptionLabel"><label for="address_type_both">{$lng.lbl_both}</label></td>
</tr>
</table>
    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_city}:</td>
    <td width="10"><font class="CustomerMessage"></font></td>
    <td><input type="text" maxlength="64" name="posted_data[city]" value="{$search_prefilled.city}" style="width:70%" /></td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_state}:</td>
    <td width="10"><font class="CustomerMessage"></font></td>
    <td>{include file="main/states.tpl" states=$states name="posted_data[state]" default=$search_prefilled.state required="N" style="style='width:70%'"}</td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_country}:</td>
    <td width="10"><font class="CustomerMessage"></font></td>
    <td>
    <select name="posted_data[country]" style="width:70%">
        <option value="">[{$lng.lbl_please_select_one}]</option>
{section name=country_idx loop=$countries}
        <option value="{$countries[country_idx].country_code}"{if $search_prefilled.country eq $countries[country_idx].country_code} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
    </select>
    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_zipcode}:</td>
    <td width="10"><font class="CustomerMessage"></font></td>
    <td>
<input type="text" maxlength="32" name="posted_data[zipcode]" value="{$search_prefilled.zipcode}" style="width:70%" />
{if $search_prefilled eq "" or $search_prefilled.address_type eq ""}
<script type="text/javascript" language="JavaScript 1.2">
<!--
managedate('address',true);
-->
</script>
{/if}
    </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_phone}/{$lng.lbl_fax}:</td>
    <td width="10"><font class="CustomerMessage"></font></td>
    <td><input type="text" maxlength="32" name="posted_data[phone]" value="{$search_prefilled.phone}" style="width:70%" /></td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_email}:</td>
    <td width="10">&nbsp;</td>
    <td><input type="text" maxlength="128" name="posted_data[email]" value="{$search_prefilled.email}" style="width:70%" /></td>
</tr>

{/if}

<tr>
    <td colspan="2">&nbsp;</td>
    <td>
    <br /><br />
    <input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" onclick="javascript: cw_submit_form(this, '');" />
    &nbsp;&nbsp;&nbsp;
    <input type="button" value="{$lng.lbl_reset|strip_tags:false|escape}" onclick="javascript: reset_form('searchform', searchform_def);" />
    </td>
</tr>

</table>

    </td>
</tr>

</table>
</form>

{if $search_prefilled.need_advanced_options}
<script type="text/javascript" language="JavaScript 1.2">
<!--
visibleBox('1');
-->
</script>
{/if}

{/capture}
{include file="common/section.tpl" title=$title content=$smarty.capture.section extra='width="100%"'}
