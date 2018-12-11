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
});
// {/literal}
</script>


<div id='errors'>{if $address_errors}{include file='common/dialog_message.tpl' alt_content=$address_errors}{/if}</div>
<strong>{if $address.is_main}{$lng.lbl_billing_address}{elseif $address.is_current}{$lng.lbl_shipping_address}{/if}</strong>



<input type='hidden' name='{$name_prefix}[address_id]' value='{if $address_type}{$address_type}{else}{$address.address_id}{/if}' />
{include file='main/users/fields/title.tpl'}
{include file='main/users/fields/firstname.tpl'}
{include file='main/users/fields/lastname.tpl'}



{include file='main/users/fields/address.tpl'}
{include file='main/users/fields/address_2.tpl'}


            {include file='main/users/fields/city.tpl'}

            {include file='main/users/fields/state.tpl'}

            {include file='main/users/fields/zipcode.tpl'}

            {include file='main/users/fields/country.tpl'}

            {include file='main/users/fields/county.tpl'}

            {include file='main/users/fields/region.tpl'}

            {include file='main/users/fields/phone.tpl'}

            {include file='main/users/fields/fax.tpl'}


{include file='main/users/sections/custom.tpl' included_tab='address' fv=$address.custom_fields name_prefix="`$name_prefix`[custom_fields]"}
{if $address.address_id}<label class="save_as_new">{$lng.lbl_save_as_new} <input type='checkbox' name='{$name_prefix}[as_new]' value='1' /></label>{/if}
</div>
