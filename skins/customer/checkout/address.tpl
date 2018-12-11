<!-- 2 address columns on checkout -->
<table width='100%' cellpadding='0' cellspacing='0'>
    <tr valign='top'>
        <td width='50%'><h2>{$lng.lbl_billing_address}</h2></td>
        <td width='50%'><h2>{$lng.lbl_shipping_address}</h2></td>
    <tr valign='top'>
        <td>&nbsp;</td>
        <td class="same"><label><input type="checkbox" id="is_same" name="update_fields[is_same_address]" value="1"{if $userinfo.main_address.address_id eq $userinfo.current_address.address_id} checked{/if}
        onclick="$('#current_address').css('display', this.checked?'none':''); cw_checkout_same_address();" /> {$lng.lbl_shipping_as_billing}</label></td>
    </tr>
    <tr valign='top'>
        <td>
            <div class="grey_boxes">
                <div id="main_address">
                <!-- cw@checkout_address_label_main [ -->
                {include file='main/users/address_label.tpl' address=$userinfo.main_address}
                <!-- cw@checkout_address_label_main ] -->
                <a id="checkout_main_address_edit_link" href='index.php?target=user&mode=addresses&action=load&address_type=main&is_checkout=1' class='control ajax'>Edit</a>
                {*include file='main/users/sections/address_select.tpl' addresses=$userinfo.addresses value=$userinfo.main_address.address_id name="update_fields[address][main_address_id]" is_main=1*}
                </div>
            </div>
        </td>
        <td>
            <div class="grey_boxes">
                <div id="current_address" {if $is_checkout && $userinfo.main_address.address_id eq $userinfo.current_address.address_id} style="display: none"{/if}>
                <!-- cw@checkout_address_label_current [ -->
                {include file='main/users/address_label.tpl' address=$userinfo.current_address}
                <!-- cw@checkout_address_label_current ] -->
                <a id="checkout_curr_address_edit_link" href='index.php?target=user&mode=addresses&action=load&address_type=current&is_checkout=1' class='control ajax'>Edit</a>
                {*include file='main/users/sections/address_select.tpl' addresses=$userinfo.addresses value=$userinfo.current_address.address_id name="update_fields[address][current_address_id]" is_main=0*}
                </div>
            </div>
        </td>
    </tr>
</table>
