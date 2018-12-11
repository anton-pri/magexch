<div id='address_modify'>
{if !$name_prefix}
	{capture assign='name_prefix'}update_fields[address][{$address.address_id|default:0}]{/capture}
{/if}

<script type="text/javascript">
    var country = '{$address.country}';
    var state = '{$address.state}';
    var id = '{$name_prefix}';
// {literal}
$(document).ready(function() {
    cw_address_init(country,state,id);
    if (is_checkout) {
        $('#apply_address').show();
    }
});
// {/literal}
</script>


<div id='errors'>{if $address_errors}{include file='common/dialog_message.tpl' alt_content=$address_errors}{/if}</div>
<strong>{if $address.is_main}{$lng.lbl_billing_address}{elseif $address.is_current}{$lng.lbl_shipping_address}{/if}</strong>

<div class="form-horizontal push-20-t">
<input type='hidden' name='{$name_prefix}[address_id]' value='{if $address.address_id}{$address.address_id}{else}{$address_type}{/if}' />
{include file='admin/users/fields/title.tpl'}
{include file='admin/users/fields/firstname.tpl'}
{include file='admin/users/fields/lastname.tpl'}

{include file='admin/users/fields/address.tpl'}
{include file='admin/users/fields/address_2.tpl'}

{include file='admin/users/fields/city.tpl'}

{include file='admin/users/fields/state.tpl'}

{include file='admin/users/fields/zipcode.tpl'}

{include file='admin/users/fields/country.tpl'}

{include file='admin/users/fields/county.tpl'}

{include file='admin/users/fields/region.tpl'}

{include file='admin/users/fields/phone.tpl'}

{include file='admin/users/fields/fax.tpl'}

{include file='main/users/sections/custom.tpl' included_tab='address' fv=$address.custom_fields name_prefix="`$name_prefix`[custom_fields]"}
</div>
{if $address.address_id && !$is_checkout}<label class="save_as"><span>Save as new</span> <input type='checkbox' name='{$name_prefix}[as_new]' value='1' /></label>{/if}
</div>
