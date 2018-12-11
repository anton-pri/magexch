{include_once_src file='main/include_js.tpl' src='js/change_user_type_ajax.js'}

{if $current_area eq 'G'}
{include file='buttons/button.tpl' button_title=$lng.lbl_print_invoice href="javascript: print_invoice();"}<br/>
{$lng.lbl_pos_doc_print_warning}
{/if}

<form action="index.php?target={$current_target}" method="post" name="editcustomer_form">
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="action" value="update_customer" />
<input type="hidden" name="show" value="customer" />
<input type="hidden" name="doc_id" value="{$doc_id}" />

<div class="form-horizontal">

{if $current_area ne 'C'}
{include file='common/subheader.tpl' title=$lng.lbl_customer_info}

<table class="table table-striped table-borderless table-header-bg" width="100%">
<thead>
<tr{cycle name=c1 values=', class="cycle"'}>
    <th width="20%">&nbsp;</td>
    <th width="40%">{$lng.lbl_aom_current_value}</th>
    <th>{$lng.lbl_aom_original_value}</th>
</tr>
</thead>

<tr{cycle name=c1 values=', class="cycle"'}>
    <td style="vertical-align: top;">{$lng.lbl_user_id}</td>
    <td nowrap>
{if $order.type eq 'P' || $order.type eq 'R' || $order.type eq 'Q'}
    {include file='admin/select/find_user.tpl' name='customer_info[customer_id]' value=$cart_customer.customer_id form='editcustomer_form' area='S'}
{else}
    {include file='admin/select/find_user.tpl' name='customer_info[customer_id]' value=$cart_customer.customer_id form='editcustomer_form' area='C'}
{/if}
    </td>
    <td style="vertical-align: top;">{$customer.customer_id}</td>
</tr>
{/if}

<tr>
    <td colspan="3">{include file='common/subheader.tpl' title=$lng.lbl_personal_information}</td>
<tr>
<thead>
  <tr>
	<th>&nbsp;</th>
	<th>{$lng.lbl_aom_current_value}</th>
	<th>{$lng.lbl_aom_original_value}</th>
  </tr>
</thead>

{if $profile_fields.basic.company.is_avail}
<tr{cycle name=c1 values=', class="cycle"'}>
	<td>{$lng.lbl_company}</td>
	<td><input type="text" name="customer_info[additional_info][company]" size="32" maxlength="32" value="{$cart_customer.company|escape}" class="form-control" /></td>
	<td>{$customer.company}</td>
</tr>
{/if}

<tr{cycle name=c1 values=', class="cycle"'}>
    <td>{$lng.lbl_email}</td>
    <td><input type="text" name="customer_info[email]" size="32" maxlength="32" value="{$cart_customer.email|escape}" class="form-control" /></td>
    <td>{$customer.email}</td>
</tr>

{if $profile_fields.basic.tax_number.is_avail}
<tr{cycle name=c1 values=', class="cycle"'}>
	<td>{if $customer.usertype eq 'R'}{$lng.lbl_tax_number_reseller}{else}{$lng.lbl_tax_number}{/if}</td>
	<td><input type="text" name="customer_info[additional_info][tax_number]" size="32" maxlength="32" value="{$cart_customer.tax_number|escape}" class="form-control" /></td>
	<td>{$customer.tax_number}</td>
</tr>
{/if}

{if $profile_fields.basic.ssn.is_avail}
<tr{cycle name=c1 values=', class="cycle"'}>
    <td>{$lng.lbl_ssn}</td>
    <td><input type="text" name="customer_info[additional_info][ssn]" size="32" maxlength="32" value="{$cart_customer.ssn|escape}" class="form-control" /></td>
    <td>{$customer.ssn}</td>
</tr>
{/if}

{if $current_area ne 'C'}
<tr{cycle name=c1 values=', class="cycle"'}>
    <td>{$lng.lbl_usertype}</td>
    <td>{include file='admin/select/usertype.tpl' name='customer_info[usertype]' value=$cart_customer.usertype limit=$order.type onchange="cw_user_ajax_change_memberships(this.value, 'customer_infomembership_id')"}</td>
    <td>{include file='admin/select/usertype.tpl' mode='static' value=$customer.usertype limit=$order.type}</td>
</tr>

<tr{cycle name=c1 values=', class="cycle"'}>
	<td>{$lng.lbl_membership}</td>
	<td>{include file='admin/select/membership.tpl' name='customer_info[membership_id]' value=$cart_customer.membership_id}</td>
	<td>{$customer.membership|default:$lng.lbl_not_member}</td>
</tr>
{/if}

{if $profile_sections.address.is_avail}
<tr>
    <td colspan="3">{include file='common/subheader.tpl' title=$lng.lbl_billing_address}</td>
</tr>
<thead>
    <tr>
  	<th>&nbsp;</th>
	<th>{$lng.lbl_aom_current_value}</th>
	<th>{$lng.lbl_aom_original_value}</th>
    </tr>
</thead>

<tr class="cycle" style="display: none;">
    <td>{$lng.lbl_address}</td>
    <td>{include file='main/select/address.tpl' name='customer_info[main_address][address_id]' customer_id=$cart_customer.customer_id}</td>
    <td>&nbsp;</td>
</tr>
{include file="admin/users/address_aom_edit.tpl" name_prefix="customer_info[main_address]" address=$cart_customer.main_address original=$customer.main_address}

<tr>
    <td colspan="3">    
{capture name=sh_title}
<div class="form-group">
  <div class="col-xs-12">
    <label class="checkbox-inline">
      <input type="checkbox" name="customer_info[current_address][same_as_main]" value="1"{if $cart_customer.current_address.same_as_main} checked{/if} onclick="javascript: if(this.checked) $('.same_as_main').hide(); else $('.same_as_main').show();"/>
      {$lng.lbl_same_as_billing}
    </label>
  </div>
</div>
{/capture}
        {include file='common/subheader.tpl' title=$lng.lbl_shipping_address right=$smarty.capture.sh_title}
    </td>
</tr>
<thead class="same_as_main"{if $cart_customer.current_address.same_as_main} style="display:none"{/if}>
<tr>
	<th>&nbsp;</th>
	<th>{$lng.lbl_aom_current_value}</th>
	<th>{$lng.lbl_aom_original_value}</th>
</tr>
<thead>
<tbody class="same_as_main"{if $cart_customer.current_address.same_as_main} style="display:none"{/if}>

<tr class="cycle" style="display: none;">
    <td>&nbsp;</td>
    <td>{include file='admin/select/address.tpl' name='customer_info[current_address][address_id]' customer_id=$cart_customer.customer_id}</td>
    <td>&nbsp;</td>
</tr>
{include file="admin/users/address_aom_edit.tpl" name_prefix="customer_info[current_address]" address=$cart_customer.current_address original=$customer.current_address}
</tbody>
{/if}
</table>

</div>

<div class="row">
    <div class="col-xs-12">{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('editcustomer_form');" button_title=$lng.lbl_update style="btn push-20 btn-minw btn-default btn-green"}</div>
</div>
<div class="clear"></div>
</form>
