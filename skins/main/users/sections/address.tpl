{* TODO: move zipcode check to validator *}
{* include_once file='js/check_zipcode_js.tpl' *}

{if !$name_prefix}
{assign var='name_prefix' value='update_fields[address]'}
{/if}
{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}

<script type="text/javascript">
var addresses_list = new Array();
var lbl_add_new = '{$lng.lbl_add_new}';
var user = '{$userinfo.customer_id}';
var reg_usertype = '{$userinfo.usertype}';
{if $is_checkout}
{literal}
$(document).ready(function() {
	setTimeout("cw_checkout_init()", 100);
});
{/literal}
{/if}
</script>

<div id='address_book_wrapper' title='Address book'>
{include file='main/users/address_book.tpl' class='address_book' addresses=$userinfo.addresses}
</div>

{if $is_checkout}
<div id='address' href='{$form_url}/index.php?target=user&mode=addresses&action=save&user={$userinfo.customer_id}&is_checkout=1'>
    {include file='customer/checkout/address.tpl'}
</div>
{else}
<div class="grey_boxes">
{include file='main/users/sections/address_select.tpl' addresses=$userinfo.addresses value=$userinfo.main_address.address_id name="update_fields[address][main_address_id]" is_main=1}
</div>

{/if}



