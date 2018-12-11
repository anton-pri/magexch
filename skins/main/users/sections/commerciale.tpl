{if $profile_fields.commerciale.division_id.is_avail}
<div class="input_field_{$profile_fields.commerciale.division_id.is_required}">
    <label>{$lng.lbl_division}</label>
    {include file='main/select/warehouse.tpl' name='update_fields[commerciale][division_id]' value=$userinfo.additional_info.division_id}
    {if $fill_error.commerciale.division_id}<span class="field_error">&lt;&lt;</span>{/if}
</div>
{/if}

{if $profile_fields.commerciale.salesman_customer_id.is_avail}
<div class="input_field_{$profile_fields.commerciale.salesman_customer_id.is_required}">
    <label>{$lng.lbl_salesman}</label>
    {include file="main/select/salesman.tpl" name="update_fields[commerciale][salesman_customer_id]" value=$userinfo.relations.salesman_customer_id}
    {if $fill_error.commerciale.salesman_customer_id}<span class="field_error">&lt;&lt;</span>{/if}
</div>
{/if}

{if $profile_fields.commerciale.parent_customer_id.is_avail}
<div class="input_field_{$profile_fields.commerciale.parent_customer_id.is_required}">
    <label>{$lng.lbl_parent_salesman}</label>
    {*include file="main/select/salesman.tpl" name="update_fields[commerciale][parent_customer_id]" value=$userinfo.relations.parent_customer_id*}
	<select name="update_fields[commerciale][parent_customer_id]">
	<option value="" selected>{$lng.lbl_select_salesman}</option>
	{foreach from=$salesmans key=membership item=sales_group}
	<optgroup label="{$membership}">
	{foreach from=$sales_group item=salesman}
	<option value="{$salesman.customer_id}" {if $userinfo.salesman_info.parent_customer_id eq $salesman.customer_id} selected{/if}>#{$salesman.customer_id} {$salesman.email}</option>
	{/foreach}
	{/foreach}
	</select>
    {if $fill_error.commerciale.parent_customer_id}<span class="field_error">&lt;&lt;</span>{/if}
</div>
{/if}

{if $profile_fields.commerciale.warehouse_customer_id.is_avail}
<div class="input_field_{$profile_fields.commerciale.warehouse_customer_id.is_required}">
    <label>{$lng.lbl_warehouse}</label>
    {include file='main/select/warehouse.tpl' name='update_fields[commerciale][warehouse_customer_id]' value=$userinfo.relations.warehouse_customer_id}
    {if $fill_error.commerciale.warehouse_customer_id}<span class="field_error">&lt;&lt;</span>{/if}
</div>
{/if}

{if $profile_fields.commerciale.doc_prefix.is_avail}
<div class="input_field_{$profile_fields.commerciale.doc_prefix.is_required}">
    <label>{$lng.lbl_doc_prefix}</label>
    <input type="text" name="update_fields[commerciale][doc_prefix]" value="{$userinfo.additional_info.doc_prefix}">
    {if $fill_error.commerciale.doc_prefix}<span class="field_error">&lt;&lt;</span>{/if}
</div>
{/if}

{if $profile_fields.commerciale.order_entering_format.is_avail}
<div class="input_field_{$profile_fields.commerciale.order_entering_format.is_required}">
    <label>{$lng.lbl_order_entering_format}</label>
    <input type="checkbox" name="update_fields[commerciale][order_entering_format]" value="1"{if $userinfo.additional_info.order_entering_format} checked{/if}>
    {if $fill_error.commerciale.order_entering_format}<span class="field_error">&lt;&lt;</span>{/if}
</div>
{/if}

{include file='main/users/sections/custom.tpl'}
