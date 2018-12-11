{if $smarty.get.mode neq 'add'}

{assign var='maxvalue' value='999999'}
{*
{if $type eq "D"}
{include file='common/subheader.tpl' title=$lng.lbl_shipping_charges content=$smarty.capture.section}
{else}
{include file='common/subheader.tpl' title=$lng.lbl_shipping_markups content=$smarty.capture.section}
{/if}
*}
{capture name=section}
{capture name=block}

<p>
Shipping formula:<br />
<pre>
SHIPPING = Flat_Rate + (TOTAL_WEIGHT * Weight_Rate) + (ITEMS * Item_Rate) + (SUBTOTAL * Percent_Rate / 100)
</pre>
</p>

<form action="index.php" method="get" name="zone_form" class="form-horizontal">
<input type="hidden" name="target" value="{$current_target}" />
<input type="hidden" name="type" value="{$type}" />

{*if $current_area eq 'A'}
{include file='main/select/warehouse.tpl' name='division_id' value=$smarty.get.division_id onchange='document.zone_form.submit()' is_please_select=1}
{/if*}
<div class="select_rate form-group">
	<div class="col-xs-12">{include file='main/select/shipping.tpl' name='shipping_id' value=$smarty.get.shipping_id onchange='document.zone_form.submit()' is_please_select=1}</div>
</div>
<div class="select_rate form-group">
	<div class="col-xs-12">{include file='main/select/zone.tpl' name='zone_id' value=$smarty.get.zone_id onchange='document.zone_form.submit()'}</div>
</div>
</form>

{if $shipping_rates_avail gt 0}

<form action="index.php?target={$current_target}" method="post" name="shipping_rates_form">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="zone_id" value="{$smarty.get.zone_id|escape:"html"}" />
<input type="hidden" name="shipping_id" value="{$smarty.get.shipping_id|escape:"html"}" />
{if $current_area eq 'A'}
<input type="hidden" name="division_id" value="{$smarty.get.division_id|escape:"html"}" />
{/if}
<input type="hidden" name="type" value="{$type}" />

{assign var='tmp_shipping_methods' value=0}
{foreach from=$zones_list item='zone'}

{if $zone.shipping_methods}
<div class="clear"></div>
<div class="box">
    {include file='common/subheader.tpl' title=$zone.zone.zone class="grey"}
    <label><input type='checkbox' class='select_all' class_to_select='{$zone.zone.zone_id}rates_item' style="margin:0 8px;" /> Select all</label>
    {assign var='tmp_shipping_methods' value=1}

    <table class="table table-striped dataTable vertical-center" width="100%">
    {foreach key=shipid item=shipping_method from=$zone.shipping_methods}

<script type="text/javascript" language="JavaScript 1.2">
    <!--
checkboxes{$zone.zone.zone_id}_{$shipid} = new Array({section name=rate loop=$shipping_method.rates}{if not %rate.first%},{/if}'posted_data[{$shipping_method.rates[rate].rate_id}][to_delete]'{/section});
-->
</script>
<thead>
<tr>
    <th width="1">
        <input type="checkbox" id="sm_{$zone.zone.zone_id}_{$shipid}" name="sm_{$zone.zone.zone_id}_{$shipid}" onclick="javascript:select_all_checkboxes(this.checked, 'shipping_rates_form', checkboxes{$zone.zone.zone_id}_{$shipid});" class="{$zone.zone.zone_id}rates_item" />
    </th>
    <th>{$shipping_method.shipping|trademark}</th>
</tr>
</thead>
    {foreach from=$shipping_method.rates item=shipping_rate}
<tr valign="top" class="form-horizontal">
	<td style="vertical-align: top;">
        <img src="{$ImagesDir}/spacer.gif" width="10" height="1" alt="" /><input type="checkbox" name="posted_data[{$shipping_rate.rate_id}][to_delete]" />
       </td>
	<td>

        <div class="form-group form-inline">
	    <label class="col-xs-12">{$lng.lbl_weight_range} ({$config.General.weight_symbol})</label>
	    <div class="col-xs-12">
        	<div class="form-group"><input type="text" class="form-control" name="posted_data[{$shipping_rate.rate_id}][minweight]" size="9" value="{$shipping_rate.minweight|formatprice}" /></div>
        	<div class="form-group"> - </div>
        	<div class="form-group"><input type="text" class="form-control" name="posted_data[{$shipping_rate.rate_id}][maxweight]" size="9" value="{$shipping_rate.maxweight|formatprice}" /></div>
        </div>
        </div>

        <div class="form-group form-inline">
            <label class="col-xs-12">
                {$lng.lbl_subtotal_range}
            </label>
            <div class="col-xs-12">
           		<div class="form-group">
                <select class="form-control" name='posted_data[{$shipping_rate.rate_id}][apply_to]' style="width:258px;">
                <option value='ST'  {if $shipping_rate.apply_to eq 'ST'}selected{/if} >{$lng.lbl_subtotal}</option>
                <option value='DST' {if $shipping_rate.apply_to eq 'DST'}selected{/if}>{$lng.lbl_discounted_subtotal}</option>
                </select>
                </div>
                <div class="form-group"><input type="text" class="form-control" name="posted_data[{$shipping_rate.rate_id}][mintotal]" size="9" value="{$shipping_rate.mintotal|formatprice}" /></div>
            	<div class="form-group"> - </div>
            	<div class="form-group"><input type="text" class="form-control" name="posted_data[{$shipping_rate.rate_id}][maxtotal]" size="9" value="{$shipping_rate.maxtotal|formatprice}" /></div>
			</div>
        </div>
<div class="form-horizontal">
<table class="form-group" width='100%'>
    <tr>
        <td class="form-group">
	    <label class="col-xs-12">{$lng.lbl_flat_charge} ({$config.General.currency_symbol})</label>
	    <div class="col-xs-12">
	    	<input type="text" class="form-control" name="posted_data[{$shipping_rate.rate_id}][rate]" size="5" value="{$shipping_rate.rate|formatprice}" />
		</div>
        </td>
        
        <td class="form-group">
            <label class="col-xs-12">{$lng.lbl_per_weight_charge|substitute:"weight":$config.General.weight_symbol}</label>
            <div class="col-xs-12">
            	<input type="text" class="form-control" name="posted_data[{$shipping_rate.rate_id}][weight_rate]" size="5" value="{$shipping_rate.weight_rate|formatprice}" />
			</div>
        </td>
        
        <td class="form-group">
        <label class="col-xs-12">{$lng.lbl_over_weight} ({$config.General.weight_symbol})</label>
        <div class="col-xs-12">
        	<input type="text" class="form-control" name="posted_data[{$shipping_rate.rate_id}][overweight]" size="5" value="{$shipping_rate.overweight|formatprice}" />
        </div>
        </td>

    </tr>
    <tr>
        <td class="form-group last">
            <label class="col-xs-12">{$lng.lbl_percent_charge}</label>
            <div class="col-xs-12">
            	<input type="text" class="form-control" name="posted_data[{$shipping_rate.rate_id}][rate_p]" size="5" value="{$shipping_rate.rate_p|formatprice}" />
            </div>
        </td>
        
        <td class="form-group last">
            <label class="col-xs-12">{$lng.lbl_per_item_charge}</label>
            <div class="col-xs-12">
            	<input type="text" class="form-control" name="posted_data[{$shipping_rate.rate_id}][item_rate]" size="5" value="{$shipping_rate.item_rate|formatprice}" />
			</div>
        </td>


        <td class="form-group last">
        <label class="col-xs-12">{$lng.lbl_per_weight_for_overweight|substitute:"weight":$config.General.weight_symbol}</label>
        <div class="col-xs-12">
        	<input type="text" class="form-control" name="posted_data[{$shipping_rate.rate_id}][overweight_rate]" size="5" value="{$shipping_rate.overweight_rate|formatprice}" />
		</div>
        </td>
    </tr>
</table>  
</div>      
    </td>
</tr>
    {/foreach}
{/foreach}
</table>

</div>
{/if}
{/foreach}

</form>
{/if}

{if !$tmp_shipping_methods}
    {if $type eq "D"}{$lng.lbl_no_shipping_rates_defined}{else}{$lng.lbl_no_shipping_markups_defined}{/if}
{/if}

<div id="sticky_content" class="buttons">
{if $tmp_shipping_methods}
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('shipping_rates_form');" style="btn-green push-20 push-5-r"}
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('shipping_rates_form', 'delete');" style="btn-danger push-20 push-5-r"}
{/if}
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=shipping_rates&mode=add" style="btn-green push-20 push-5-r"}
</div>
{/capture}

{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{if $type eq "D"}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_shipping_charges local_config='Shipping'}
{else}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_shipping_markups local_config='Shipping'}
{/if}

{else} {* if mode == 'add' *}

{*if $type eq 'D'}{include file='common/subheader.tpl' title=$lng.lbl_add_shipping_charge_values}{else}{include file='common/subheader.tpl' title=$lng.lbl_add_shipping_markup_values}{/if*}
{capture name=section}
{capture name=block2}

<div class="box">
{if $shippings}
<form action="index.php?target={$current_target}" method="post" name="add_shipping_rate" class="form-horizontal">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="zone_id" value="{$zone_id}" />
<input type="hidden" name="shipping_id" value="{$shipping_id}" />
{if $current_area eq 'A'}
<input type="hidden" name="division_id" value="{$smarty.get.division_id}" />
{/if}
<input type="hidden" name="type" value="{$type}" />


<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_shipping_method}</label>
	<div class="col-xs-12">
    	{include file='main/select/shipping.tpl' name='shipping_id_new' is_please_select=1}
    </div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_zone}</label>
	<div class="col-xs-12">
    	{include file='main/select/zone.tpl' name='zone_id_new'}
    </div>
</div>
<div class="form-group form-inline">
	<label class="col-xs-12">{$lng.lbl_weight_range} ({$config.General.weight_symbol})</label>
	<div class="col-xs-12">
    	<div class="form-group"><input type="text" class="form-control" name="minweight_new" size="9" value="{0|formatprice}" /></div>
    	<div class="form-group"> - </div>
    	<div class="form-group"><input type="text" class="form-control" name="maxweight_new" size="9" value="{$maxvalue|formatprice}" /></div>
    </div>
</div>
<div class="form-group form-inline">
	<label class="col-xs-12">
        {$lng.lbl_subtotal_range}
    </label>
    <div class="col-xs-12">
    <div class="form-group">
        <select name='apply_to_new' class="form-control">
        <option value='ST'>{$lng.lbl_subtotal}</option>
        <option value='DST'>{$lng.lbl_discounted_subtotal}</option>
        </select>
    </div>
    <div class="form-group">
    	<input type="text" class="form-control" name="mintotal_new" size="9" value="{0|formatprice}" />
    </div>
    <div class="form-group"> - </div>
    <div class="form-group">
    	<input type="text" class="form-control" name="maxtotal_new" size="9" value="{$maxvalue|formatprice}" />
    </div>
    </div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_flat_charge} ({$config.General.currency_symbol})</label>
	<div class="col-xs-6 col-md-2">
		<input type="text" class="form-control" name="rate_new" size="5" value="{0|formatprice}" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_percent_charge}</label>
	<div class="col-xs-6 col-md-2">
		<input type="text" class="form-control" name="rate_p_new" size="5" value="{0|formatprice}" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_per_item_charge}</label>
	<div class="col-xs-6 col-md-2">
		<input type="text" class="form-control" name="item_rate_new" size="5" value="{0|formatprice}" />
	</div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_per_weight_charge|substitute:"weight":$config.General.weight_symbol}</label>
    <div class="col-xs-6 col-md-2">
		<input type="text" class="form-control" name="weight_rate_new" size="5" value="{0|formatprice}" />
	</div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_over_weight} ({$config.General.weight_symbol})</label>
    <div class="col-xs-6 col-md-2">
    	<input type="text" class="form-control" name="overweight_new" size="5" value="{0|formatprice}" />
    </div>
</div>
<div class="form-group last">
    <label class="col-xs-12">{$lng.lbl_per_weight_for_overweight|substitute:"weight":$config.General.weight_symbol}</label>
    <div class="col-xs-6 col-md-2">
    	<input type="text" class="form-control" name="overweight_rate_new" size="5" value="{0|formatprice}" />
    </div>
</div>
<br/>


</form>
</div>
<div class="buttons">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add href="javascript: cw_submit_form('add_shipping_rate');" style="btn-green push-20"}</div>

{elseif $type eq "D"}
{$lng.txt_shipping_charge_rtc_note}
{/if}
{/capture}

{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2}
{/capture}
{if $type eq 'D'}{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_add_shipping_charge_values}{else}{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_add_shipping_markup_values}{/if}
{/if}
