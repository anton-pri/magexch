<div id='{$included_tab}_section' {if $included_tab ne 'basic_search'}style="display: none;"{/if}>
{if $included_tab eq 'basic_search'}
{* start *}
<div class="box form-horizontal">
{include file="common/subheader.tpl" title=$lng.lbl_search}

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_search_for_pattern}:</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" name="posted_data[basic_search][substring]"  value="{$search_prefilled.basic_search.substring}" />
	</div>
</div>
<div class="form-group">

    <label class="col-xs-12">{$lng.lbl_search_in}:</label>

	<div class="search_options col-xs-12">
	  <div class="checkbox">
    	<label><input type="checkbox" name="posted_data[basic_search][by_firstname]"{if $search_prefilled.basic_search.by_firstname} checked="checked"{/if} />
    	{$lng.lbl_firstname}&nbsp;</label>
	  </div>
	  <div class="checkbox">
    	<label><input type="checkbox" name="posted_data[basic_search][by_lastname]"{if $search_prefilled.basic_search.by_lastname} checked="checked"{/if} />
    	{$lng.lbl_lastname}&nbsp;</label>
	  </div>
	  <div class="checkbox">
    	<label><input type="checkbox" name="posted_data[basic_search][by_email]"{if $search_prefilled.basic_search.by_email} checked="checked"{/if} />
    	{$lng.lbl_email}&nbsp;</label>
	  </div>
	  <div class="checkbox">
    	<label><input type="checkbox" name="posted_data[basic_search][by_customer_id]"{if $search_prefilled.basic_search.by_customer_id} checked="checked"{/if} />
    	{$lng.lbl_user_id}&nbsp;</label>
	  </div>
{tunnel func='cw_user_search_get_register_fields' via='cw_call' param1=$usertype_search param2='T' assign='text_search_register_fields'}
{foreach from=$text_search_register_fields item=register_field key=register_field_id}
          <div class="checkbox">
        {assign var='search_field_name_register_field' value="by_register_field_`$register_field_id`"}
        <label><input type="checkbox" name="posted_data[basic_search][{$search_field_name_register_field}]"{if $search_prefilled.basic_search.$search_field_name_register_field} checked="checked"{/if} />
        {$register_field|replace:'_':' '|capitalize}&nbsp;</label>
          </div>
{/foreach}

	</div>
</div>

</div>

{elseif $included_tab eq 'adv_search_address'}
{* start *}
<div class="box form-horizontal">
{include file="common/subheader.tpl" title=$lng.lbl_search_customer_by_address}
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_search_by_address}:</label>
    <div class="search_options col-xs-12">
 	<div class="radio">
    	<label><input type="radio" name="posted_data[address][type]" value="1"{if $search_prefilled.address.type eq 1} checked="checked"{/if} />{$lng.lbl_main_address}</label>
	</div>
	<div class="radio">
    	<label><input type="radio" name="posted_data[address][type]" value="2"{if $search_prefilled.address.type eq 2} checked="checked"{/if} />{$lng.lbl_current_address}</label>
	</div>
	<div class="radio">
    	<label><input type="radio" name="posted_data[address][type]" value="3"{if $search_prefilled.address.type eq 3} checked="checked"{/if} />{$lng.lbl_billing} {$lng.lbl_or} {$lng.lbl_shipping}</label>
	</div>
	<div class="radio">
    	<label><input type="radio" name="posted_data[address][type]" value="4"{if $search_prefilled.address.type eq 4} checked="checked"{/if} />{$lng.lbl_any}</label>
	</div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_city}:</label>
    <div class="col-xs-12">
    	<input class="form-control" type="text" maxlength="64" name="posted_data[address][city]" value="{$search_prefilled.address.city}" />
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_country}:</label>
    <div class="col-xs-12">
    	{include file='main/map/countries.tpl' countries=$countries name="posted_data[address][country]" default=$search_prefilled.address.country state_name="posted_data[address][state]" state_value=$search_prefilled.address.state state_enabled=1}
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_state}:</label>
    <div class="col-xs-12">
   		{include file='main/map/states.tpl' name="posted_data[address][state]" default=$search_prefilled.address.state required="N" multiple=true}
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_phone}:</label>
    <div class="col-xs-12">
        <input type="text" class="form-control" maxlength="16" name="posted_data[address][phone]" value="{$search_prefilled.address.phone}" />
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_zipcode}:</label>
    <div class="col-xs-6 col-md-2">
        <input type="text" class="form-control" maxlength="16" name="posted_data[address][zipcode]" value="{$search_prefilled.address.zipcode}" />
    </div>
</div>

</div>

{elseif $included_tab eq 'adv_search_admin'}
{* start *}
<div class="box form-horizontal">
{include file="common/subheader.tpl" title=$lng.lbl_administration}

<div class="form-group form-inline">
    <label class="col-xs-12">
        <input type="checkbox" name="posted_data[admin][by_create_date]" value="1" {if $search_prefilled.admin.by_create_date eq 'Y'}checked{/if} />
        {$lng.lbl_profile_created_between}
    </label>
    <div class="col-xs-12">
    	<div class="form-group">{include file="main/select/date.tpl" name="posted_data[admin][creation_date_start]" value=$search_prefilled.admin.creation_date_start}</div>
    	<div class="form-group"> - </div>
    	<div class="form-group">{include file="main/select/date.tpl" name="posted_data[admin][creation_date_end]" value=$search_prefilled.admin.creation_date_end}</div>
    </div>
</div>
<div class="form-group form-inline">
    <label class="col-xs-12">
        <input type="checkbox" name="posted_data[admin][by_modify_date]" value="1" {if $search_prefilled.admin.by_modify_date}checked{/if} />
        {$lng.lbl_profile_modified_between}
    </label>
    <div class="col-xs-12">
    	<div class="form-group">{include file="main/select/date.tpl" name="posted_data[admin][modify_date_start]" value=$search_prefilled.admin.modify_date_start}</div>
    	<div class="form-group"> - </div>
    	<div class="form-group">{include file="main/select/date.tpl" name="posted_data[admin][modify_date_end]" value=$search_prefilled.admin.modify_date_end}</div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_membership}</label>
    <div class="col-xs-12">
    	<select class="form-control" size='5' multiple='multiple' name='posted_data[admin][membership][]'>
    	{foreach from=$memberships item=membership}
       		<option value='{$membership.membership_id}' {if in_array($membership.membership_id,(array)$search_prefilled.admin.membership)}selected{/if}>{$membership.membership}</option>
    	{/foreach}
    	</select>
    </div>
</div>

{tunnel func='cw_user_search_get_register_fields' via='cw_call' param1=$usertype_search param2='C' assign='checkbox_search_register_fields'}
{foreach from=$checkbox_search_register_fields item=register_field key=register_field_id}
   {assign var='search_field_name_register_field' value="by_register_field_`$register_field_id`"}
   <div class="form-group">
        <label class="col-xs-12" for="{$search_field_name_register_field}" style="float:left;display:block;width:auto;padding-right:2px">{$register_field|replace:'_':' '|capitalize}:&nbsp;</label>
        <input type="checkbox" name="posted_data[admin][{$search_field_name_register_field}]" id="{$search_field_name_register_field}" {if $search_prefilled.admin.$search_field_name_register_field} checked="checked"{/if} />
   </div>
{/foreach}

</div>
{elseif $included_tab eq 'adv_search_orders'}
<div class="form-horizontal">

{include file="common/subheader.tpl" title=$lng.lbl_orders}

<div class="form-group form-inline">
    <label class="col-xs-12">{$lng.lbl_order_id} #:</label>
    <div class="col-xs-12">
    	<div class="form-group"><input type="text" class="form-control" name="posted_data[orders][order_from]"  value="{$search_prefilled.orders.order_from}" /></div>
    	<div class="form-group"> - </div>
    	<div class="form-group"><input type="text" class="form-control" name="posted_data[orders][order_to]"  value="{$search_prefilled.orders.order_to}" /></div>
	</div>
</div>
<div class="form-group form-inline">
    <label class="col-xs-12">{$lng.lbl_order_date}:</label>
    <div class="col-xs-12">
    	<div class="form-group">{include file="main/select/date.tpl" name="posted_data[orders][order_date_start]" value=$search_prefilled.orders.order_date_start}</div>
    	<div class="form-group"> - </div>
    	<div class="form-group">{include file="main/select/date.tpl" name="posted_data[orders][order_date_end]" value=$search_prefilled.orders.order_date_end}</div>
	</div>
</div>

<div class="form-group form-inline">
    <label class="col-xs-12">{$lng.lbl_orders_count|default:'Orders count per customer'}:</label>
    <div class="col-xs-12">
    	<div class="form-group"><input type="text" class="form-control" name="posted_data[orders][orders_count_from]"  value="{$search_prefilled.orders.orders_count_from}" /></div>
    	<div class="form-group"> - </div>
    	<div class="form-group"><input type="text" class="form-control" name="posted_data[orders][orders_count_to]"  value="{$search_prefilled.orders.orders_count_to}" /> </div>
	</div>
</div>

<div class="form-group form-inline">
    <label class="col-xs-12">{$lng.lbl_avg_subtotal|default:'Average orders subtotal per customer'}:</label>
    <div class="col-xs-12">
    	<div class="form-group"><input type="text" class="form-control" name="posted_data[orders][avg_subtotal_from]"  value="{$search_prefilled.orders.avg_subtotal_from}" /></div>
    	<div class="form-group"> - </div>
    	<div class="form-group"><input type="text" class="form-control" name="posted_data[orders][avg_subtotal_to]"  value="{$search_prefilled.orders.avg_subtotal_to}" /></div>
    </div>
</div>

<div class="form-group form-inline">
    <label class="col-xs-12">{$lng.lbl_total_spent|default:'Total spent per customer'}:</label>
    <div class="col-xs-12">
    	<div class="form-group"><input type="text" class="form-control" name="posted_data[orders][total_spent_from]"  value="{$search_prefilled.orders.total_spent_from}" /></div>
    	<div class="form-group"> - </div>
    	<div class="form-group"><input type="text" class="form-control" name="posted_data[orders][total_spent_to]"  value="{$search_prefilled.orders.total_spent_to}" /></div>
    </div>
</div>


{* TODO: Replace this old fashion fields by JS list and AJAX pop-up with proudct search *}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_product}:</label>
	<div class="col-xs-12">{product_selector name_for_id='orders_product' name_for_name='orders_product_name' form='search_form'}</div>
</div>

<div class="form-group form-inline">
    <label class="col-xs-12">{$lng.lbl_product_price|default:'Product price'}:</label>
    <div class="col-xs-12">
      <div class="form-group"><input type="text" class="form-control" name="posted_data[orders][product_price_from]"  value="{$search_prefilled.orders.product_price_from}" /></div>
      <div class="form-group"> - </div>
      <div class="form-group"><input type="text" class="form-control" name="posted_data[orders][product_price_to]"  value="{$search_prefilled.orders.product_price_to}" /></div>
    </div>
</div>


<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_categories}</label>
    <div class="col-xs-12">{include file='admin/select/category.tpl' name='posted_data[orders][category_ids][]' value=$search_prefilled.orders.category_ids disabled=$read_only multiple=true}</div>
</div>


<div class="form-group">
    <label class="col-xs-12">
    {$lng.lbl_attributes}
    </label>
    <div class="col-xs-12">{include file='admin/select/attributes_multiple.tpl' name='posted_data[orders][attributes]' value=$search_prefilled.orders.attributes}</div>
</div>

<!-- cw@user_search_by_orders -->

</div>
{/if}
</div>

