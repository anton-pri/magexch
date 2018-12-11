{if $included_tab eq 'search_orders'}
{* start *}
{literal}
<script type="text/javascript">
    $(document).ready(function(){
        $("input[name='posted_data[basic][created]']").change(function(){
                if($(this).val()=='selected'){
                    $('.selected_dates').fadeIn();
                }else{
                    $('.selected_dates').hide();
                }
            })
});
</script>
{/literal}
<div class="box">

<div class="input_field_0">

    {if $docs_type eq 'I'}
        <label>{$lng.lbl_quote_created_between}</label>
    {else}
        <label>{$lng.lbl_order_created_between}</label>
    {/if}

    <div class="float-left ">
        <label class="checkbox"><input type="radio" name="posted_data[basic][created]" value="any_date"{if $search_prefilled.basic.created eq '' or $search_prefilled.basic.created eq 'any_date'} checked="checked"{/if} />
            {$lng.lbl_any_date}&nbsp;</label>

        <label class="checkbox"><input type="radio" name="posted_data[basic][created]" value="this_month"{if $search_prefilled.basic.created eq 'this_month'} checked="checked"{/if} />
            {$lng.lbl_this_month}&nbsp;</label>

        <label class="checkbox"><input type="radio" name="posted_data[basic][created]" value="this_week"{if $search_prefilled.basic.created eq 'this_week'} checked="checked"{/if} />
            {$lng.lbl_this_week}&nbsp;</label>
        <label class="checkbox"><input type="radio" name="posted_data[basic][created]" value="today"{if $search_prefilled.basic.created eq 'today'} checked="checked"{/if} />
            {$lng.lbl_today}&nbsp;</label>
        <label class="checkbox"><input type="radio" name="posted_data[basic][created]" value="selected"{if $search_prefilled.basic.created eq 'selected'} checked="checked"{/if} />
            {$lng.lbl_selected_dates}&nbsp;</label>
    </div>

</div>
    <div class="input_field_0 selected_dates" {if $search_prefilled.basic.created ne 'selected'}style="display:none"{/if}>
        <label> {$lng.lbl_selected_dates}&nbsp;</label>
        {include file="main/select/date.tpl" name="posted_data[basic][creation_date_start]" value=$search_prefilled.basic.creation_date_start} -
        {include file="main/select/date.tpl" name="posted_data[basic][creation_date_end]" value=$search_prefilled.basic.creation_date_end}
        </div>

    <div class="input_field_0">
    {if $docs_type eq 'I'}
    	<label>{$lng.lbl_quote_status}</label>
    	{include file="main/select/doc_i_status.tpl" status=$search_prefilled.basic.status mode="select" name="posted_data[basic][status]" extended="Y"}
    {else}
    	<label>{$lng.lbl_order_status}</label>
    	{include file="main/select/doc_status.tpl" status=$search_prefilled.basic.status mode="select" name="posted_data[basic][status]" extended="" multiple=1 normal_array=1 extra="size=\"10\""}
    {/if}
</div>
<div class="input_field_0">
	{if $docs_type eq 'I'}
	    <label>{$lng.lbl_quote_id}</label>
    {else}
    	<label>{$lng.lbl_order_id}</label>
    {/if}
    <input type="text" name="posted_data[basic][doc_id_start]" size="10" maxlength="15" value="{$search_prefilled.basic.doc_id_start}" class="width153" /> -
    <input type="text" name="posted_data[basic][doc_id_end]" size="10" maxlength="15"value="{$search_prefilled.basic.doc_id_end}" class="width153" />
</div>

</div>

{elseif $included_tab eq 'search_orders_advanced'}

<div class="box">

<div class="input_field_0">
    <label>
    {if $docs_type eq 'I'}
    	{$lng.lbl_quote_total}
    {else}
    	{$lng.lbl_order_total}
    {/if} ({$config.General.currency_symbol})</label>
    <input type="text" size="10" maxlength="15" name="posted_data[advanced][total_start]" value="{$search_prefilled.advanced.total_start|formatprice}" class="width153" /> -
    <input type="text" size="10" maxlength="15" name="posted_data[advanced][total_end]" value="{$search_prefilled.advanced.total_end|formatprice}" class="width153" />
</div>
{*
<div class="input_field_0">
    <label>{$lng.lbl_quote_expired}</label>
    {include file='main/select/date.tpl' name='posted_data[advanced][expire_date_start]' value=$search_prefilled.advanced.expire_date_start} -
    {include file='main/select/date.tpl' name='posted_data[advanced][expire_date_end]' value=$search_prefilled.advanced.expire_date_end}
</div>
*}
<div class="input_field_0">
    <label>{$lng.lbl_payment_method}</label>
    {include file='main/select/payment.tpl' name='posted_data[advanced][payment_id][]' value=$search_prefilled.advanced.payment_id is_please_select=0 multiple=true}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_delivery}</label>
    {include file='main/select/shipping.tpl' name='posted_data[advanced][shipping_id][]' value=$search_prefilled.advanced.shipping_id is_please_select=0 multiple=true}
</div>


<div class="input_field_0">
    <label>{$lng.lbl_search_for_pattern}</label>
    <input type="text" name="posted_data[products][product]" size="30" value="{$search_prefilled.products.product}" />
</div>
<!-- cw@after_product_field -->
<div class="input_field_0">
    <label>{$lng.lbl_sku}</label>
    <input type="text" maxlength="64" name="posted_data[products][product_code]" value="{$search_prefilled.products.product_code}" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_product_id}#</label>
    <input type="text" maxlength="64" name="posted_data[products][product_id]" value="{$search_prefilled.products.product_id}" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_price} ({$config.General.currency_symbol})</label>
    <input type="text" size="10" maxlength="15" name="posted_data[products][price_start]" value="{$search_prefilled.products.price_start|formatprice}" class="width153" /> -
    <input type="text" size="10" maxlength="15" name="posted_data[products][price_end]" value="{$search_prefilled.products.price_end|formatprice}" class="width153" />
</div>


{if $current_area eq 'A'}
{* start *}


<div class="input_field_0">
    <label>{$lng.lbl_customer}</label>
    <input type="text" name="posted_data[customer][substring]"  value="{$search_prefilled.customer.substring}" />
</div>

<div class="input_field_0">
    <label>{$lng.lbl_search_in}</label>
    
    <div class="float-left width570px">
    <label class="checkbox"><input type="checkbox" name="posted_data[customer][by_customer_id]"{if $search_prefilled eq "" or $search_prefilled.customer.by_customer_id} checked="checked"{/if} />
    {$lng.lbl_customer_id}&nbsp;</label>

    <label class="checkbox"><input type="checkbox" name="posted_data[customer][by_firstname]"{if $search_prefilled eq "" or $search_prefilled.customer.by_firstname} checked="checked"{/if} />
    {$lng.lbl_firstname}&nbsp;</label>

    <label class="checkbox"><input type="checkbox" name="posted_data[customer][by_lastname]"{if $search_prefilled eq "" or $search_prefilled.customer.by_lastname} checked="checked"{/if} />
    {$lng.lbl_lastname}&nbsp;</label>

    <label class="checkbox"><input type="checkbox" name="posted_data[customer][by_email]"{if $search_prefilled eq "" or $search_prefilled.customer.by_email} checked="checked"{/if} />
    {$lng.lbl_email}&nbsp;</label>
    </div>
</div>
<div class="input_field_0">
    <label>{$lng.lbl_search_by_address}</label>
    
    <div class="float-left width570px">
    <label class="checkbox"><input type="radio" name="posted_data[customer][type]" value="1"{if $search_prefilled eq '' or $search_prefilled.customer.type eq 1} checked="checked"{/if} />
    {$lng.lbl_main_address}&nbsp;</label>

    <label class="checkbox"><input type="radio" name="posted_data[customer][type]" value="2"{if $search_prefilled.customer.type eq 2} checked="checked"{/if} />
    {$lng.lbl_current_address}&nbsp;</label>

    <label class="checkbox"><input type="radio" name="posted_data[customer][type]" value="3"{if $search_prefilled.customer.type eq 3} checked="checked"{/if} />
    {$lng.lbl_both}&nbsp;</label>
    </div>

</div>

<div class="input_field_0">
    <label>{$lng.lbl_city}</label>
    <input type="text" maxlength="64" name="posted_data[customer][city]" value="{$search_prefilled.customer.city}" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_country} {$search_prefilled.customer.country}</label>
    {include file="main/map/countries.tpl" countries=$countries name="posted_data[customer][country]" default=$search_prefilled.customer.country state_name="posted_data[customer][state]" state_enabled=1}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_state}</label>
    {include file='main/map/states.tpl' name='posted_data[customer][state]' default=$search_prefilled.customer.state required="N"}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_zipcode}</label>
    <input type="text" maxlength="16" name="posted_data[customer][zipcode]" value="{$search_prefilled.customer.zipcode}" />
</div>

{/if}

</div>

{include file='main/docs/additional_search_field.tpl'}

{/if}
