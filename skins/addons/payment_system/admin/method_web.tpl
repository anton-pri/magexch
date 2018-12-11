<div class="box form-horizontal">


<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_web}
        <input type="hidden" name="posted_data[is_web]" value="0" />
    	<input type="checkbox" name="posted_data[is_web]" value="1"{if $payment.is_web} checked{/if}/>
    </label>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_protocol}</label>
    <div class="col-xs-12">
    <select name="posted_data[protocol]" class="form-control">
        <option value="http"{if $payment.protocol eq "http"} selected="selected"{/if}>HTTP</option>
        <option value="https"{if $payment.protocol eq "https"} selected="selected"{/if}>HTTPS</option>
    </select>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_cash_on_delivery_method} <input type="checkbox" id="is_cod_{$payment.payment_id}" name="posted_data[is_cod]" value="1" {if $payment.is_cod} checked="checked"{/if} /></label>
</div>

{if $payment.web.is_down}
<div class="field_error">{$lng.txt_cc_processor_requirements_failed|substitute:"processor":$payment.web.addon}</div>
{/if}

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_payment_gateways}</label>
    <div class="col-xs-12">
	<select name="posted_data[processor]" class="form-control">
		<option value="">{$lng.lbl_please_select}</option>
{foreach from=$cc_addons item=addon}
		<option value="{$addon.addon}" {if $payment.web.processor eq $addon.addon} selected{/if}>{$addon.addon}</option>
{/foreach}
		</optgroup>
	</select>
	</div>
</div>

{* kornev, it's possible to move the settings to the cw_payment_settings if required *}
{*if $payment.web.processor}
{/if*}

</div>
