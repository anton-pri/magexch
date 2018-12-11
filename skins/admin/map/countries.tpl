{*include file='common/page_title.tpl' title=$lng.lbl_countries*}
{capture name=section}
{capture name=block}

<p>{$lng.txt_countries_management_top_text}</p>

{section name=zone loop=$zones}
<span class="countries_group">
{if $zones[zone].zone eq $zone or ($zones[zone].zone eq "ALL" and $zone eq "")}
<b class="label label-default">{$zones[zone].title}</b>
{else}
<a class="label label-primary" href="index.php?target=countries{if $zones[zone].zone}&zone={$zones[zone].zone}{/if}">{$zones[zone].title}</a>
{/if}
</span>
 &nbsp; 
{/section}

<br /><br />

{include file="common/navigation.tpl"}


<form action="index.php?target=countries" method="post" name="countries_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page" value="{$smarty.get.page}" />
<input type="hidden" name="zone" value="{$zone}" />

<div class="box">

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr>
	<th class="text-center">{$lng.lbl_code}</th>
	<th>{$lng.lbl_country}</th>
    <th class="text-center">{$lng.lbl_language}</th>
    <th class="text-center">{$lng.lbl_regions}/{$lng.lbl_state}/{$lng.lbl_county}/{$lng.lbl_city}</th>
	<th class="text-center">{$lng.lbl_active}</th>
</tr>
</thead>
{foreach from=$countries item=country}

<tr{if $country.code eq $config.Company.country} class="TableHead"{else}{cycle values=', class="cycle"'}{/if}>
	<td nowrap="nowrap" align="center">{$country.code}</td>
	<td><input type="text" class="form-control" size="18" maxlength="50" name="posted_data[{$country.code}][country]" value="{$country.country}" /></td>
    <td align="center">
        {include file="admin/select/language.tpl" name="posted_data[`$country.code`][lang]" value=$country.lang}
    </td>
    <td align="center" class="display_checkbox text-center">
      <div class="geo_checkbox col-xs-3">
        <input type="checkbox" name="posted_data[{$country.code}][display_regions]" value="1"{if $country.display_regions} checked="checked"{/if} />
        {if $country.display_regions}
        <a href="index.php?target={$current_target}&mode=regions&country={$country.code}">{$lng.lbl_regions}</a>
        {/if}
      </div>
      <div class="geo_checkbox col-xs-3">
        <input type="checkbox" name="posted_data[{$country.code}][display_states]" value="1"{if $country.display_states} checked="checked"{/if} />
        {if $country.display_states}
        <a href="index.php?target={$current_target}&mode=states&country={$country.code}">{$lng.lbl_states}</a>
        {/if}
      </div>
      <div class="geo_checkbox col-xs-3">
	  <input type="checkbox" name="posted_data[{$country.code}][display_counties]" value="1"{if $country.display_counties} checked="checked"{/if} />
        {if $country.display_counties}
        <a href="index.php?target={$current_target}&mode=counties&country={$country.code}">{$lng.lbl_counties}</a>
        {/if}
      </div>
      <div class="geo_checkbox col-xs-3">
        <input type="checkbox" name="posted_data[{$country.code}][display_cities]" value="1"{if $country.display_cities} checked="checked"{/if} />
        {if $country.display_cities}
        <a href="index.php?target={$current_target}&mode=cities&country={$country.code}">{$lng.lbl_cities}</a>
        {/if}
      </div>




    </td>
	<td align="center"><input type="checkbox" name="posted_data[{$country.code}][active]" value="1"{if $country.active} checked="checked"{/if} /></td>
</tr>
{/foreach}
</table>

</div>

{include file="common/navigation.tpl"}

<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('countries_form');" acl='__2507' style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_deactivate_all href="javascript:cw_submit_form('countries_form', 'deactivate_all');" acl='__2507' style="btn-danger push-20 push-5-r"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_activate_all href="javascript:cw_submit_form('countries_form', 'activate_all');" acl='__2507' style="btn-green push-20 push-5-r"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block title=$lng.lbl_countries}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_countries}

