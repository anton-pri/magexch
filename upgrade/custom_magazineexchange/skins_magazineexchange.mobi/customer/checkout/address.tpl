<!-- 2 address columns on checkout -->
            <h2>{$lng.lbl_billing_address}</h2>
            <div class="grey_boxes">
                <div id="main_address">
                {include file='main/users/address_label.tpl' address=$userinfo.main_address}
                <a href='index.php?target=user&mode=addresses&action=load&address_type=main&is_checkout=1' class='control ajax'>Edit</a>
                {*include file='main/users/sections/address_select.tpl' addresses=$userinfo.addresses value=$userinfo.main_address.address_id name="update_fields[address][main_address_id]" is_main=1*}
                </div>
            </div>
            <h2>{$lng.lbl_shipping_address}</h2>
            <div class="grey_boxes">
                <div id="current_address" {if $is_checkout && $userinfo.main_address.address_id eq $userinfo.current_address.address_id} style="display: none"{/if}>
                {include file='main/users/address_label.tpl' address=$userinfo.current_address}
                <a href='index.php?target=user&mode=addresses&action=load&address_type=current&is_checkout=1' class='control ajax'>Edit</a>
                {*include file='main/users/sections/address_select.tpl' addresses=$userinfo.addresses value=$userinfo.current_address.address_id name="update_fields[address][current_address_id]" is_main=0*}
                </div>
            </div>


        <div class="same"><label><input type="checkbox" id="is_same" name="update_fields[is_same_address]" value="1"{if $userinfo.main_address.address_id eq $userinfo.current_address.address_id} checked{/if}
        onclick="$('#current_address').css('display', this.checked?'none':''); cw_checkout_same_address();" /> {$lng.lbl_shipping_as_billing}</label></div>

