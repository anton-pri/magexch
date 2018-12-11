{capture name=section}
<br />

<form action="index.php?target=ups" method="post" name="upsconfigureform">
<input type="hidden" name="action" value="{$mode}" />

<table cellpadding="2" cellspacing="2" width="100%">

<tr>
	<td width="95" align="center" valign="top">{include file="addons/UPS_OnLine_Tools/ups_logo.tpl"}</td>
	<td>&nbsp;</td>
	<td width="100%">

{if $config.Shipping.realtime_shipping ne "Y" or $config.Shipping.use_intershipper eq "Y"}
<font class="ErrorMessage">{$lng.txt_ups_rss_warning}</font>
<br /><br />
{/if}

<table width="100%" cellpadding="3" cellspacing="1">

<tr>
	<td colspan="2">{include file="common/subheader.tpl" title=$lng.lbl_ups_rss}</td>
</tr>

<tr>
	<td colspan="2">{$lng.txt_fields_are_mandatory}</td>
</tr>

<!--
<tr>
	<td><b>{$lng.lbl_ups_shipper_number}:</b></td>
	<td>
		<input type="text" name="shipper_number" value="{$shipping_options.rss.shipper_number|escape}" />
	</td>
</tr>
-->

<tr>
	<td width="50%"><b>{$lng.lbl_ups_rss_merchant_account_type}:<font class="Star">*</font></b><br />
	<a href="index.php?target=void(0);" onclick="javascript:window&action=UPS','UPS_HELP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');" title="{$lng.lbl_open_new_window|escape}" class="SmallNote">{$lng.lbl_click_here_for_help}</a></td>
	<td width="50%">
		<select name="account_type" size="3" style="width:200">
			<option value="01" {if $shipping_options.rss.account_type eq "01" or $shipping_options.rss.account_type eq ""} selected="selected"{/if}>Daily Pickup</option>
			<option value="02" {if $shipping_options.rss.account_type eq "02"} selected="selected"{/if}>Occasional</option>
			<option value="03" {if $shipping_options.rss.account_type eq "03"} selected="selected"{/if}>Suggested Retail Rates</option>
		</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_packaging_type}:</b></td>
	<td>
		<select name="packaging_type" style="width:200">
			<option value="00" {if $shipping_options.rss.packaging_type eq "00"} selected="selected"{/if}>Unknown</option>
			<option value="02" {if $shipping_options.rss.packaging_type eq "02"} selected="selected"{/if}>Package</option>
			<option value="01" {if $shipping_options.rss.packaging_type eq "01"} selected="selected"{/if}>UPS Letter / UPS Express Envelope</option>
			<option value="03" {if $shipping_options.rss.packaging_type eq "03"} selected="selected"{/if}>UPS Tube</option>
			<option value="04" {if $shipping_options.rss.packaging_type eq "04"} selected="selected"{/if}>UPS Pak</option>
			<option value="21" {if $shipping_options.rss.packaging_type eq "21"} selected="selected"{/if}>UPS Express Box</option>
			<option value="24" {if $shipping_options.rss.packaging_type eq "24"} selected="selected"{/if}>UPS 25 Kg Box&#174;</option>
			<option value="25" {if $shipping_options.rss.packaging_type eq "25"} selected="selected"{/if}>UPS 10 Kg Box&#174;</option>
			<option value="30" {if $shipping_options.rss.packaging_type eq "30"} selected="selected"{/if}>Pallet (for GB or PL domestic shipments only)</option>

		</select>
	</td>
</tr>

<tr>
	<td colspan="2"><br />{include file="common/subheader.tpl" title="`$lng.lbl_ups_package_dim`<font class='Star'>*</font>" class="grey"}</td>
</tr>

<tr>
	<td colspan="2">
	
	{$lng.txt_ups_package_dim_note}
	
	<br /><br />

	<table cellpadding="2" cellspacing="1">
	<tr>
		<td nowrap="nowrap"><b>{$lng.lbl_length} x {$lng.lbl_width} x {$lng.lbl_height} ({$shipping_options.rss.dim_units}):</b></td>
		<td nowrap="nowrap">
<input type="text" name="length" value="{$shipping_options.rss.length}" size="7" />
x
<input type="text" name="width" value="{$shipping_options.rss.width}" size="7" />
x
<input type="text" name="height" value="{$shipping_options.rss.height}" size="7" />
		</td>
	</tr>

	</table>

	</td>
</tr>	

<tr>
	<td colspan="2"><br />{include file="common/subheader.tpl" title=$lng.lbl_service_options class="grey"}</td>
</tr>

<tr>
	<td colspan="2">
		<table cellpadding="2" cellspacing="1">
		<tr>
			<td><input type="checkbox" name="upsoptions[]" value="AH" {if $shipping_options.rss.AH eq "Y"} checked="checked"{/if} /></td>
			<td><b>{$lng.lbl_ups_additional_handling}</b></td>
		</tr>
		<tr>	
			<td><input type="checkbox" name="upsoptions[]" value="SP" {if $shipping_options.rss.SP eq "Y"} checked="checked"{/if} /></td>
			<td><b>{$lng.lbl_ups_saturday_pickup}</b></td>
		</tr>
		<tr>
			<td><input type="checkbox" name="upsoptions[]" value="SD" {if $shipping_options.rss.SD eq "Y"} checked="checked"{/if} /></td>
			<td><b>{$lng.lbl_ups_saturday_delivery}</b></td>
		</tr>
		</table>
	</td>
</tr>

<tr>
	<td colspan="2"><br />{include file="common/subheader.tpl" title=$lng.lbl_ups_declared_value class="grey"}</td>
</tr>

<tr>
	<td><b>{$lng.lbl_package_insured_value}:</b></td>
	<td>
		<input type="text" name="iv_amount" value="{$shipping_options.rss.iv_amount}" style="width:200" />
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_insured_value_currency}:</b></td>
	<td>
		<select name="iv_currency" style="width:200">
		{include file="addons/UPS_OnLine_Tools/ups_currency.tpl" selected=$shipping_options.rss.iv_currency}
		</select>
	</td>
</tr>

<tr>
	<td colspan="2"><br />{include file="common/subheader.tpl" title=$lng.lbl_ups_cod class="grey"}</td>
</tr>

<tr>
	<td colspan="2">{$lng.txt_ups_cod_note}</td>
</tr>

<tr>
	<td><b>{$lng.lbl_ups_cod_value}:</b></td>
	<td>
		<input type="text" name="codvalue" value="{$shipping_options.rss.codvalue}" />
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_ups_cod_value_currency}:</b></td>
	<td>
		<select name="cod_currency" style="width:200">
		{include file="addons/UPS_OnLine_Tools/ups_currency.tpl" selected=$shipping_options.rss.cod_currency}
		</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_ups_cod_funds}:</b></td>
	<td>
		<select name="cod_funds_code" style="width:200">
			<option value="0"{if $shipping_options.rss.cod_funds_code eq "0"} selected="selected"{/if}>{$lng.lbl_ups_cod_funds_code_0}</option>
			<option value="8"{if $shipping_options.rss.cod_funds_code eq "8"} selected="selected"{/if}>{$lng.lbl_ups_cod_funds_code_8}</option>
		</select>
	</td>
</tr>

<tr>
	<td colspan="2"><br />{include file="common/subheader.tpl" title=$lng.lbl_delivery_confirmation class="grey"}</td>
</tr>

<tr>
	<td><b>{$lng.lbl_delivery_confirmation}:</b></td>
	<td>
		<select name="delivery_conf" style="width:200">
		<option value="0">{$lng.lbl_ups_no_confirmation}</option>
		<option value="1"{if $shipping_options.rss.delivery_conf eq 1} selected="selected"{/if}>{$lng.lbl_ups_no_signature}</option>
		<option value="2"{if $shipping_options.rss.delivery_conf eq 2} selected="selected"{/if}>{$lng.lbl_ups_signature_required}</option>
		<option value="3"{if $shipping_options.rss.delivery_conf eq 3} selected="selected"{/if}>{$lng.lbl_ups_adult_signature}</option>
		</select>
	</td>
</tr>

<tr>
	<td colspan="2"><br />{include file="common/subheader.tpl" title=$lng.lbl_ups_convertion_rate class="grey"}</td>
</tr>

<tr>
	<td><b>{$lng.lbl_shipping_cost_convertion_rate}:</b><br />
	<font class="SmallText">{$lng.txt_shipping_conversion_rate}</font>
	</td>
	<td valign="top">
		<input type="text" name="conversion_rate" value="{$shipping_options.rss.conversion_rate|default:"1"}" style="width:200" />
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_shipping_cost_currency}:</b><br />
	<font class="SmallText">{$lng.txt_shipping_cost_currency}</font>
	</td>
	<td valign="top">
		<b>{$shipping_options.rss.currency_code|default:"Unknown"}</b>
	</td>
</tr>

<tr>
	<td colspan="2">&nbsp;</td>
</tr>

<tr>
	<td colspan="2">{include file="common/subheader.tpl" title=$lng.lbl_ups_av}</td>
</tr>

<tr>
	<td><b>{$lng.lbl_ups_av_status}:</b></td>
	<td>
		<select name="av_status" style="width:200">
		<option value="Y"{if $shipping_options.rss.av_status eq "Y"} selected="selected"{/if}>{$lng.lbl_enabled}</option>
		<option value="N"{if $shipping_options.rss.av_status eq "N"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
		</select>
	</td>
</tr>

<tr>
	<td><b>{$lng.lbl_ups_av_quality}:</b></td>
	<td>
		<select name="av_quality" style="width:200">
		<option value="exact"{if $shipping_options.rss.av_quality eq "exact"} selected="selected"{/if}>{$lng.lbl_exact_match}</option>
		<option value="very_close"{if $shipping_options.rss.av_quality eq "very_close"} selected="selected"{/if}>{$lng.lbl_very_close_match}</option>
		<option value="close"{if $shipping_options.rss.av_quality eq "close"} selected="selected"{/if}>{$lng.lbl_close_match}</option>
		<option value="possible"{if $shipping_options.rss.av_quality eq "possible"} selected="selected"{/if}>{$lng.lbl_possible_match}</option>
		<option value="poor"{if $shipping_options.rss.av_quality eq "poor"} selected="selected"{/if}>{$lng.lbl_poor_match}</option>
		</select>
	</td>
</tr>

<tr>
	<td colspan="2">{$lng.txt_ups_av_note}</td>
</tr>

<tr>
	<td colspan="2"><br /><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>
</table>

<br /><br />

{$lng.txt_ups_av_note2}

<br /><br />

<div align="right">
<table>
<tr>
	<td>{include file='buttons/button.tpl' button_title=$lng.lbl_main_page style='btn' href="index.php?target=ups"}</td>
	<td><img src="{$ImagesDir}/spacer.gif" width="50" height="1" alt="" /></td>
	<td>{include file='buttons/button.tpl' button_title=$lng.lbl_test style='btn' href="index.php?target=test_realtime_shipping" target="_blank"}</td>
	<td><img src="{$ImagesDir}/spacer.gif" width="50" height="1" alt="" /></td>
</tr>
</table>
</div>

	</td>
</tr>

</table>
</form>

<br />
<hr />
<div align="center">
{$lng.txt_ups_trademark_text}
</div>
{/capture}
{include file="common/section.tpl" title=$title content=$smarty.capture.section}
