{capture name=section}
{capture name=block}

<p>{$lng.txt_destination_zones_note}</p>

<script type="text/javascript" language="JavaScript 1.2">
<!--
var zones = {ldelim}{rdelim};
{counter name='js' start='-1' print=false}
{foreach from=$rest_zones key=z item=v}
zones.{$z} = {ldelim}{foreach from=$v item=c key=k}{if $k > 0},{/if}{$c}:'Y'{/foreach}{rdelim};
{/foreach}

{literal}
function normalizeSelect(name) {
	var tmp = document.getElementById(name);
	if (tmp)
		tmp.options[tmp.options.length-1] = null;
}
{/literal}
var msg_err_zone_rename='{$lng.msg_err_zone_rename|escape}';
-->
</script>
{include_once_src file='main/include_js.tpl' src='main/zones/zone_edit.js'}

<form action="index.php?target={$current_target}" method="post" name="zone_form" onsubmit="javascript: return saveSelects(new Array('_zone_countries','_zone_states','_zone_counties'));" class="form-horizontal">
<input type="hidden" name="action" value="details" />
<input type="hidden" name="zone_id" value="{$zone_id}" />

<div class="form-group">
  <div style="width: 100%;">
    <label class="col-xs-12">{$lng.lbl_zone_name}:</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" size="50" name="zone_name" value="{$zone.zone_name|escape}" {edit_on_place table='zones' field='zone_name' pk=$zone_id} />
	</div>
  </div>

<!-- cw@zone_edit_form_main -->
<div class="col-xs-12">
<table class="table" width="100%">
<thead>
<tr>
	<th colspan="3">{$lng.lbl_countries}</th>
</tr>
<tr>
	<th width="45%">{$lng.lbl_set_val}</th>
	<th>&nbsp;</th>
	<th width="45%">{$lng.lbl_unset_val}</th>
</tr>
</thead>

<tr>
	<td align="center">
<input type="hidden" id="_zone_countries_store" name="zone_countries" value="" />
<select class="form-control" id="_zone_countries" multiple="multiple" style="width: 100%;" size="{$countries_box_size}">
{section name=cid loop=$zone_countries}
	<option value="{$zone_countries[cid].code}">{$zone_countries[cid].country}</option>
{/section}
<option></option>
</select>
<script type="text/javascript">
<!--
normalizeSelect('_zone_countries');
-->
</script>
	</td>
	<td align="center">
<input type="button" class="btn btn-default fa fa-2x" value="&#xf100;" onclick="javascript: moveSelect(document.getElementById('_zone_countries'), document.getElementById('rest_countries'), 'R');" />
<br /><br />
<input type="button" class="btn btn-default fa fa-2x" value="&#xf101;" onclick="javascript: moveSelect(document.getElementById('_zone_countries'), document.getElementById('rest_countries'), 'L');" />
	</td>
	<td align="center">
<select class="form-control" id="rest_countries" multiple="multiple" style="width: 100%;" size="{$countries_box_size}">
{section name=rcid loop=$rest_countries}
	<option value="{$rest_countries[rcid].code}">{$rest_countries[rcid].country}</option>
{/section}
</select>
	</td>
</tr>

<tr>
	<td colspan="3" align="right">
<table cellpadding="3" cellspacing="1" width="100%">
<tr>
	<td class="FormButton" nowrap="nowrap" style="border:none;">{$lng.lbl_quick_select}:</td>
	<td align="right"  style="border:none;">

<table cellpadding="3" cellspacing="1" width="100%">
{assign var="counter" value=0}
{section name=zid loop=$zones}
{if $counter eq 0}<tr>{/if}
	<td align="center" nowrap="nowrap"><a href="javascript: void(0);" onclick="javascript: cw_js_check_zone('{$zones[zid].zone}', 'rest_countries')">{$zones[zid].title}</a></td>
{math equation="x+1" x=$counter assign="counter"}
{if $counter gt 2}{assign var="counter" value=0}{/if}
{if $counter eq 0}</tr>{/if}
{/section}
</table>

	</td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td colspan="3">{include file="common/subheader.tpl" title=$lng.lbl_states}</td>
</tr>

<tr>
	<th>{$lng.lbl_set_val}</th>
	<th>&nbsp;</td>
	<th>{$lng.lbl_unset_val}</th>
</tr>

<tr>
	<td align="center">
<input type="hidden" id="_zone_states_store" name="zone_states" value="" />
<select id="_zone_states" multiple="multiple" style="width: 100%;" size="{$states_box_size}" class="form-control">
{section name=sid loop=$zone_states}
	<option value="{$zone_states[sid].country_code}_{$zone_states[sid].code}">{$zone_states[sid].country|truncate:"30":"..."}: {$zone_states[sid].state}</option>
{/section}
<option></option>
</select>
<script type="text/javascript">
<!--
normalizeSelect('_zone_states');
-->
</script>
	</td>
	<td align="center">
<input type="button" class="btn btn-default fa fa-2x" value="&#xf100;" onclick="javascript: moveSelect(document.getElementById('_zone_states'), document.getElementById('rest_states'), 'R');" />
<br /><br />
<input type="button" class="btn btn-default fa fa-2x" value="&#xf101;" onclick="javascript: moveSelect(document.getElementById('_zone_states'), document.getElementById('rest_states'), 'L');" />
	</td>
	<td align="center">
<select class="form-control" id="rest_states" name="rest_states" multiple="multiple" style="width: 100%;" size="{$states_box_size}">
{section name=rsid loop=$rest_states}
	<option value="{$rest_states[rsid].country_code}_{$rest_states[rsid].code|escape}">{$rest_states[rsid].country|truncate:"17":"...":true}: {$rest_states[rsid].state}</option>
{/section}
</select>
	</td>
</tr>


{* Counties *}

{if $config.General.use_counties eq "Y" and ($zone_counties or $rest_counties)}

<tr>
	<td colspan="3">{include file="common/subheader.tpl" title=$lng.lbl_counties}</td>
</tr>

<tr>
	<th>{$lng.lbl_set_val}</th>
	<th>&nbsp;</th>
	<th>{$lng.lbl_unset_val}</th>
</tr>

<tr>
	<td align="center">
<input type="hidden" id="_zone_counties_store" name="zone_counties" value="" />
<select class="form-control" id="_zone_counties" multiple="multiple" style="width: 100%;" size="{$counties_box_size}">
{section name=ctid loop=$zone_counties}
	<option value="{$zone_counties[ctid].county_id}">{$zone_counties[ctid].country}: {$zone_counties[ctid].state}: {$zone_counties[ctid].county}</option>
{/section}
<option></option>
</select>
<script type="text/javascript">
<!--
normalizeSelect('_zone_counties');
-->
</script>
	</td>
	<td align="center">
<input type="button" value="&lt;&lt;" onclick="javascript: moveSelect(document.getElementById('_zone_counties'), document.getElementById('rest_counties'), 'R');" />
<br /><br />
<input type="button" value="&gt;&gt;" onclick="javascript: moveSelect(document.getElementById('_zone_counties'), document.getElementById('rest_counties'), 'L');" />
	</td>
	<td align="center">
<select class="form-control" id="rest_counties" name="rest_counties" multiple="multiple" style="width: 100%;" size="{$counties_box_size}">
{section name=rctid loop=$rest_counties}
	<option value="{$rest_counties[rctid].county_id}">{$rest_counties[rctid].country}: {$rest_counties[rctid].state}: {$rest_counties[rctid].county}</option>
{/section}
</select>
	</td>
</tr>

{/if}


{* City masks *}


<tr>
	<td colspan="3">{include file="common/subheader.tpl" title=$lng.lbl_cities}</td>
</tr>

<tr>
	<th>{$lng.lbl_set_val}</th>
	<th>&nbsp;</th>
	<th>{$lng.lbl_city_mask_examples}:</th>
</tr>

<tr>
	<td>{include file="main/zones/zone_element.tpl" name="zone_cities" field_type="T" box_size=$cities_box_size}</td>
	<td align="center">&nbsp;</td>
	<td>{$lng.txt_city_mask_examples}</td>
</tr>

<tr>
	<td colspan="3">{include file="common/subheader.tpl" title=$lng.lbl_zip_postal_codes}</td>
</tr>

<tr>
	<th>{$lng.lbl_set_val}</th>
	<th>&nbsp;</th>
	<th>{$lng.lbl_zipcode_mask_examples}:</th>
</tr>

<tr>
	<td>{include file="main/zones/zone_element.tpl" name="zone_zipcodes" field_type="Z" box_size=$zipcodes_box_size}</td>
	<td align="center">&nbsp;</td>
	<td>{$lng.txt_zipcode_mask_examples}</td>
</tr>


{* Address masks *}

<tr>
	<td colspan="3">{include file="common/subheader.tpl" title=$lng.lbl_addresses}</td>
</tr>

<tr>
	<th>{$lng.lbl_set_val}</th>
	<th>&nbsp;</th>
	<th>{$lng.lbl_address_mask_examples}:</th>
</tr>

<tr>
	<td>{include file="main/zones/zone_element.tpl" name="zone_addresses" field_type="A" box_size=$addresses_box_size}</td>
	<td align="center">&nbsp;</td>
	<td>{$lng.txt_address_mask_examples}</td>
</tr>
</table>
</div>
<div class="buttons col-xs-12">
	<input type="submit" class="btn btn-green" value="{$lng.lbl_save_zone_details|strip_tags:false|escape}" />
    {if $zone_id}
    &nbsp;&nbsp;
    <input type="button" class="btn btn-green" value=" {$lng.lbl_clone|strip_tags:false} " onclick="javascript: cw_submit_form('zone_form', 'clone');" />
    {/if}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$zone.zone_name}

