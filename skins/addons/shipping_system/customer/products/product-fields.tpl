<li id="estimate_shipping_container" class="product_field{cycle values=", cycle"}" blockUI="estimate_shipping_container">
	<div class="field-title">
    	<label>{$lng.lbl_ships_in|substitute:'delivery_time':$product.supplier.delivery_time}</label>
	</div>

	<div>
        <form action="index.php?target=popup-shipping" method="post" name="estimate_shipping">
            <input type="hidden" name="product_id" value="{$product.product_id}">
            <input type="hidden" name="action" value="estimate" />

            {if $product.free_shipping eq 'Y'}{$lng.lbl_free_shipping_note}<br />{/if}
            <div class="input_field_1">
                {include file='main/select/country.tpl' name='country' value=$country}
            </div>
            <div class="input_field_1">
                <input class="textbox" placeholder="zipcode" type="text" name="zipcode" value="{$zipcode}" size="14" border="0">
            </div>
            <div style="margin-top: 4px;">{include file='buttons/button.tpl' button_title=$lng.lbl_estimate_ship style='small' onclick="blockElements('estimate_shipping_container',true);submitFormAjax('estimate_shipping', $.unblockUI);"}</div>
        </form>

        <div>
            <br/>
            {if $zipcode}
                {if $shippings}

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
        </div>
	</div>
</li>
