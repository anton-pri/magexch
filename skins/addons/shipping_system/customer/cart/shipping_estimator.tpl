<div class="shipping_estimator">
<form action="index.php?target=shipping_estimator" method="post" name="estimate_cart">
<input type="hidden" name="action" value="estimate_cart" />
<div class="input_field_1 state">
    <label>{$lng.lbl_state}</label>
    {include file='main/select/state.tpl' name='state' default=$user_address.current_address.state}
</div>
<div class="input_field_1 country">
    <label>{$lng.lbl_country}</label>
    {include file='main/select/country.tpl' name='country' value=$user_address.current_address.country}
</div>
<div class="input_field_1 zipcode">
    <label>{$lng.lbl_enter_destination_zip}</label>
    <input class="textbox" type="text" name="zipcode" value="{$user_address.current_address.zipcode}" size="14" border="0">
</div>
    {include file='buttons/button.tpl' button_title=$lng.lbl_estimate style='btn' href="javascript:cw_submit_form('estimate_cart');"}
</form>

<div class="clear"></div>
</div>
