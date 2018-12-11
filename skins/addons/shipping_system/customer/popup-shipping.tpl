
<div class="shipping_estimator">

<form action="index.php?target={$current_target}" method="post" name="popup_shipping">
<input type="hidden" name="product_id" value="{$product.product_id}">
<input type="hidden" name="action" value="estimate" />


<!--h1>{$lng.lbl_shipping_and_delivery_estimation}</h1-->
{capture name=dialog}
<div class="input_field_1">
    <label>{$lng.lbl_enter_destination}</label>
    {include file='main/select/country.tpl' name='country' value=$country}
</div>
<div class="input_field_1">
    <label>{$lng.lbl_enter_destination_zip}</label>
    <input class="textbox" type="text" name="zipcode" value="{$zipcode}" size="14" border="0">
</div>
    {include file='buttons/button.tpl' button_title=$lng.lbl_estimate style='btn' href="javascript:cw_submit_form('popup_shipping');"}

{/capture}
{include file='common/section.tpl' is_dialog=1 title=$lng.lbl_shipping_and_delivery_estimation content=$smarty.capture.dialog }



<div class="top">{$lng.lbl_popup_shipping_label}</div>

{if $product.product_id}
<div class="product">
    <div class="image float-left">{include file='common/product_image.tpl' product_id=$product.product_id image=$product.image_thumb}</div>

<div class="float-left">
    <div class="title">{$product.product}</div>

    {if $zipcode}
        {if $shippings}
         <br/><br/>

        {foreach from=$shippings key=key item=shipping}
            <div>{$shipping.shipping}{if $shipping.shipping_time} ({$shipping.shipping_time}){/if} - {include file='common/currency.tpl' value=$shipping.rate}</div>
        {/foreach}
         <br/>

        {else}
        <div class="field_error">{$lng.lbl_no_shipping_for_your_zipcode}</div>
        {/if}
    {else}
        <div class="field_error">{$lng.lbl_enter_zip_above}</div>
    {/if}
    <div class="bottom">{$lng.lbl_shipping_bottom_text}</div>
</div>
{/if}
</form>
<div class="clear"></div>
</div>

