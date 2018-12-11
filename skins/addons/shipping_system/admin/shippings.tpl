{if $mode eq 'modify'}

{*if $smarty.get.shipping_id}{include file='common/page_title.tpl' title=$lng.lbl_shipping_modify}{else}{include file='common/page_title.tpl' title=$lng.lbl_shipping_add}{/if*}
{capture name=section}
{capture name=block}

<form action="index.php?target=shipping" method="post" name="shipping_method_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="shipping_id" value="{$shipping.shipping_id}" />

<div class="form-horizontal">

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_shipping_method}</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" name="update[shipping]" value="{$shipping.shipping|escape}" />
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_active}
        <input type="hidden" name="update[active]" value="0" />
    	<input type="checkbox" name="update[active]" value="1"{if $shipping.active} checked="checked"{/if} />
    </label>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_delivery_time}</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" name="update[shipping_time]" value="{$shipping.shipping_time}" />
    </div>
</div>
{if !$shipping.addon}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_carrier}</label>
    <div class="col-xs-12">
    	{include file="admin/select_carrier.tpl" name="update[carrier_id]" value=$shipping.carrier_id}
    </div>
</div>
{/if}
<div class="form-group form-inline">
    <label class="col-xs-12">{$lng.lbl_weight_limit} ({$config.General.weight_symbol})</label>
    <div class="col-xs-12">
    	<div class="form-group"><input type="text" class="form-control" name="update[weight_min]" value="{$shipping.weight_min}" /></div>
    	<div class="form-group"> - </div>
    	<div class="form-group"><input type="text" class="form-control" name="update[weight_limit]" value="{$shipping.weight_limit}" /></div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_pos}</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" name="update[orderby]" value="{$shipping.orderby}" />
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_cod}</label>
    <div class="col-xs-8 col-md-6">
    	{include file='admin/select/cod_type.tpl' name='update[cod_type_id][]' multiple=true value=$shipping.cod_type_ids}
    </div>
</div>
<div class="form-group">

    <label class="col-xs-12">{$lng.lbl_insurance}:</label>
	<div class="col-xs-12">

      <table class="table table-bordered vertical-center" style="width: auto;">
        <tr>
        <td>{$lng.lbl_basic_fee}:</td>
        <td><input type="text" class="form-control form-control-inline" size="4" name="update[fee_basic]" value="{$shipping.fee_basic}" style="width:auto;" /></td>
        <td>{$lng.lbl_up_to}: {$config.General.currency_symbol}</td>
        <td><input type="text" class="form-control form-control-inline" size="4" name="update[fee_basic_limit]" value="{$shipping.fee_basic_limit}" style="width:auto;" /></td>
        </tr>
        <tr>
        <td>{$lng.lbl_calculate_on}: {$config.General.currency_symbol}</td>
        <td><input type="text" class="form-control form-control-inline" size="4" name="update[fee_ex_flat]" value="{$shipping.fee_ex_flat}"  style="width:auto;" /></td>
        <td>{$lng.lbl_or}</td>
        <td><input type="text" class="form-control form-control-inline" size="4" name="update[fee_ex_percent]" value="{$shipping.fee_ex_percent}" style="width:auto;" />%</td>
        </tr>
      </table>

  	</div>
</div>

{include file='admin/attributes/object_modify.tpl'}

</div>

<div class="buttons"><input type="submit" value="{$lng.lbl_update}" class="btn btn-green push-20" /></div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{if $shipping.shipping_id ne ""}{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_shipping_modify}{else}{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_shipping_add}{/if}

</form>

{else}
{*include file='common/page_title.tpl' title=$lng.lbl_shipping_methods*}
{capture name=section}
{capture name=block}

<form action="index.php?target={$current_target}" name="shipping_methods_form" method="post">
<input type="hidden" name="action" value="list" />

<div class="box">

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
    <th>{$lng.lbl_delete}</th>
    <th>{$lng.lbl_active}</th>
    <th>{$lng.lbl_shipping_method}</th>
    <th>{$lng.lbl_delivery_time}</th>
    <th>{$lng.lbl_weight_limit} ({$config.General.weight_symbol})</th>
    <th>{$lng.lbl_pos}</th>
</tr>
</thead>
{foreach from=$carriers item=carrier}
<tr class="cycle">
	<td colspan="9">
        <b>{$carrier.carrier}&nbsp;</b>
        {$lng.lbl_X_from_Y_shipping_methods_enabled|substitute:"enabled":$carrier.total_enabled:"methods":$carrier.total_methods}
    </td>
</tr>
{if $carrier.shipping}
{foreach from=$carrier.shipping item=s}
<tr{cycle values=", class='cycle'"}>
    <td align="center">{if !$carrier.addon}<input type="checkbox" name="del_shippings[{$s.shipping_id}]" value="1" />{/if}</td>
    <td align="center"><input type="checkbox" name="data[{$s.shipping_id}][active]" value="1"{if $s.active} checked="checked"{/if} /></td>
	<td><a href="index.php?target={$current_target}&shipping_id={$s.shipping_id}">{$s.shipping|trademark:$insert_trademark}</a></td>
	<td align="center"><input type="text" class="form-control" name="data[{$s.shipping_id}][shipping_time]" size="6" value="{$s.shipping_time}" /></td>
	<td align="center" nowrap="nowrap"><input type="text" class="form-control form-control-inline" size="6" name="data[{$s.shipping_id}][weight_min]" value="{$s.weight_min|default:0|formatprice}" /> - <input type="text" class="form-control form-control-inline" size="6" name="data[{$s.shipping_id}][weight_limit]" value="{$s.weight_limit|formatprice}" /></td>
	<td align="center"><input type="text" class="form-control" name="data[{$s.shipping_id}][orderby]" size="4" value="{$s.orderby}" /></td>
</tr>
{/foreach}
{/if}
{/foreach}
</table>

</div>

<div id="sticky_content" class="buttons">
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('shipping_methods_form');" button_title=$lng.lbl_update style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('shipping_methods_form', 'delete');" button_title=$lng.lbl_delete style="btn-danger push-20 push-5-r"}
{include file='admin/buttons/button.tpl' href="index.php?target=shipping&shipping_id=" button_title=$lng.lbl_add_new style="btn-green push-20 push-5-r"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_shipping_methods local_config='Shipping'}
{/if}
