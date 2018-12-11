{if $shipping_calc_error}
<div class="field_error">
    {$shipping_calc_service} {$lng.lbl_err_shipping_calc}<br>
    {$shipping_calc_error}
</div>
{/if}

{if !$shipping and $need_shipping}
    {if $user_address.current_address.zipcode}<div class="field_error">{$lng.lbl_no_shipping_for_location}</div>{/if}
    {*if $customer_id}
        {include file='buttons/modify.tpl' href='index.php?target=acc_manager'}
    {/if*}
{else}
    {if !$shipping_name}{assign var="shipping_name" value="shipping_id"}{/if}

    {if $cart_warehouse}{assign var="shipping_id" value="shipping_el_`$cart_warehouse`"}
    {else}{assign var="shipping_id" value="shipping_id"}{/if}

    {if $shipping and $need_shipping}
            {if $is_radio}
            {if $is_label}<label>{$lng.lbl_delivery}<label>{/if}
            <table width="100%" cellspacing="0" cellpadding="2">
                {foreach from=$shipping item=ship}
                <tr {if $ship.shipping_id eq $cart.info.shipping_id} class="cycle"{/if}>
                <td align='left' style="width: 54px;">
                    <input type="radio" name="{$shipping_name}" value="{$ship.shipping_id}" {if $ship.shipping_id eq $cart.info.shipping_id}checked{/if} {if $onclick} onclick="{$onclick}"{/if}>
                </td>
                <td align='left' style="width: 97px;">
                    <img src="{$ImagesDir}/carrier.jpg" alt="" />
                </td>
                <td align='left'>
                    {$ship.shipping|trademark:$insert_trademark}{if $ship.shipping_time ne ""} - {$ship.shipping_time} {if $ship.shipping_time>1}{$lng.lbl_days}{else}{$lng.lbl_day}{/if}{/if}
                </td>
                <td align='right'>
                    {include file='common/currency.tpl' value=$ship.rate}
                </td>
                </tr>
                {/foreach}
            </table>
            {else}
        <div class="input_field_easy_0_1">
            <label>{$lng.lbl_delivery|escape:"hexentity"}</label>
            <select name="{$shipping_name}" id="{$shipping_id}"{if $onchange} onchange="{$onchange}"{/if}>
            {foreach from=$shipping item=ship}
                <option value="{$ship.shipping_id}"{if $ship.shipping_id eq $cart.info.shipping_id} selected="selected"{/if}>
                {$ship.shipping|trademark:$insert_trademark:"alt"}
                ({if $ship.rate eq 0}{$lng.lbl_free}{else}{include file='common/currency.tpl' value=$ship.rate plain_text_message=1}{/if})
                {if $ship.shipping_time ne ""} - {$ship.shipping_time} {if $ship.shipping_time>1}{$lng.lbl_days}{else}{$lng.lbl_day}{/if}{/if}
                </option>
            {/foreach}
            </select>
        </div>
        {/if}
    {/if}
{/if}

{if !$customer_id}
<a href="index.php?target=shipping_estimator" class='ajax'>{$lng.lbl_estimate_ship_note}</a>
<div id="shipping_estimator_dialog" title="{$lng.lbl_shipping_and_delivery_estimation}" style='display:none'>
{include file='addons/shipping_system/customer/cart/shipping_estimator.tpl'}
</div>
{/if}
