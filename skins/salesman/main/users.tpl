{include file='common/page_title.tpl' title=$lng.lbl_users_management}
{if $mode eq "" or $users eq ""}

{include_once_src file="main/include_js.tpl" src="reset.js"}
<script type="text/javascript">
<!--
var searchform_def = [
	['posted_data[by_username]', {if $search_prefilled eq "" || $search_prefilled.by_username}true{else}false{/if}],
	['posted_data[by_firstname]', {if $search_prefilled eq "" || $search_prefilled.by_firstname}true{else}false{/if}],
	['posted_data[by_lastname]', {if $search_prefilled eq "" || $search_prefilled.by_lastname}true{else}false{/if}],
	['posted_data[by_email]', {if $search_prefilled eq "" || $search_prefilled.by_email}true{else}false{/if}],
	['posted_data[by_company]', {if $search_prefilled eq "" || $search_prefilled.by_company}true{else}false{/if}],
	['StartDay', '{$search_prefilled.start_date|default:$smarty.now|date_format:"%d"}'],
	['StartMonth', '{$search_prefilled.start_date|default:$smarty.now|date_format:"%m"}'],
	['StartYear', '{$search_prefilled.start_date|default:$smarty.now|date_format:"%Y"}'],
	['EndDay', '{$search_prefilled.end_date|default:$smarty.now|date_format:"%d"}'],
	['EndMonth', '{$search_prefilled.end_date|default:$smarty.now|date_format:"%m"}'],
	['EndYear', '{$search_prefilled.end_date|default:$smarty.now|date_format:"%Y"}'],
	['posted_data[is_export]', false],
{assign var="selected_membershipid" value=""}
{if $config.General.membership_signup eq "Y" && $search_prefilled.usertype eq "" and $search_prefilled.membershipid eq "pending_membership"}

{assign var="selected_membershipid" value="-pending_membership"}

{else}

{foreach from=$memberships item=lvls key=k}

{if $search_prefilled.usertype eq $k && $search_prefilled.membershipid eq ''}

{assign var="selected_membershipid" value=$k|cat:"-"}

{elseif $config.General.membership_signup eq "Y" && $lvls ne '' && $search_prefilled.usertype eq $k && $search_prefilled.membershipid eq "pending_membership"}

{assign var="selected_membershipid" value=$k|cat:"-pending_membership"}

{else}

{foreach from=$lvls item=v}
{if $search_prefilled.usertype eq $k && $search_prefilled.membershipid eq $v.membershipid}
{assign var="selected_membershipid" value=$k|cat:"-"|cat:$v.membershipid}
{/if}
{/foreach}

{/if}

{/foreach}

{/if}
	['posted_data[membershipid]', '{$selected_membershipid}'],
	['posted_data[registration_type]', '{$search_prefilled.registration_type}'],
	['posted_data[address_type]', '{$search_prefilled.address_type}'],
	['posted_data[city]', '{$search_prefilled.city}'],
	['posted_data[state]', '{$search_prefilled.state}'],
	['posted_data[country]', '{$search_prefilled.country}'],
	['posted_data[zipcode]', '{$search_prefilled.zipcode}'],
	['posted_data[phone]', '{$search_prefilled.phone}'],
	['posted_data[url]', '{$search_prefilled.url}'],
	['posted_data[registration_date]', '{if $search_prefilled.registration_date ne ""}Y{/if}'],
	['posted_data[last_login_date]', '{if $search_prefilled.last_login_date ne ""}Y{/if}'],
	['posted_data[date_period]', '{$search_prefilled.date_period|default:"M"}']
];
-->
</script>

<!-- SEARCH FORM START -->

<script type="text/javascript" language="JavaScript 1.2">
<!--
var date_selected = '{if $search_prefilled.date_period eq "" or $search_prefilled.date_period eq "M"}M{else}{$search_prefilled.date_period}{/if}';
{literal}
function managedate(type, status) {

	if (type == 'address')
		var fields = new Array('posted_data[city]','posted_data[state]','posted_data[country]','posted_data[zipcode]');
	else if (type == 'date')
		var fields = new Array('StartDay','StartMonth','StartYear','EndDay','EndMonth','EndYear');
	else if (type == 'date_type') {
		status = document.searchform.elements['posted_data[registration_date]'].checked + document.searchform.elements['posted_data[last_login_date]'].checked;
		status = !(status != 0);

		for (var i = 0; i < document.searchform.elements.length; i++)
			if (document.searchform.elements[i].name == 'posted_data[date_period]')
				document.searchform.elements[i].disabled = status;

		disable_dates = false;

		if (status)
			disable_dates = true;
		else if (date_selected != 'C')
			disable_dates = true;

		managedate('date', disable_dates);
		return true;

	}

	for (var i in fields)
		document.searchform.elements[fields[i]].disabled = status;
}
{/literal}
-->
</script>

{capture name=section}

<br />

<form name="searchform" action="index.php?target=users" method="post">

<table cellpadding="0" cellspacing="0" width="100%">

<tr>
	<td>

<table cellpadding="4" cellspacing="0" width="100%">

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_pattern}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
	<input type="text" name="posted_data[substring]" size="30" style="width:70%" value="{$search_prefilled.substring}" />
	&nbsp;
	<input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
	</td>
</tr>

<tr>
	<td height="10" class="FormButton">{$lng.lbl_search_in}:</td>
	<td>&nbsp;</td>
	<td>
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="checkbox" id="posted_data_by_username" name="posted_data[by_username]"{if $search_prefilled eq "" or $search_prefilled.by_username} checked="checked"{/if} /></td>
	<td class="OptionLabel"><label for="posted_data_by_username">{$lng.lbl_username}</label></td>

	<td width="5"><input type="checkbox" id="posted_data_by_firstname"name="posted_data[by_firstname]"{if $search_prefilled eq "" or $search_prefilled.by_firstname} checked="checked"{/if} /></td>
	<td class="OptionLabel"><label for="posted_data_by_firstname">{$lng.lbl_firstname}</label></td>

	<td width="5"><input type="checkbox" id="posted_data_by_lastname" name="posted_data[by_lastname]"{if $search_prefilled eq "" or $search_prefilled.by_lastname} checked="checked"{/if} /></td>
	<td class="OptionLabel"><label for="posted_data_by_lastname">{$lng.lbl_lastname}</label></td>

	<td width="5"><input type="checkbox" id="posted_data_by_email" name="posted_data[by_email]"{if $search_prefilled eq "" or $search_prefilled.by_email} checked="checked"{/if} /></td>
	<td class="OptionLabel"><label for="posted_data_by_email">{$lng.lbl_email}</label></td>

	<td width="5"><input type="checkbox" id="posted_data_by_company" name="posted_data[by_company]"{if $search_prefilled eq "" or $search_prefilled.by_company} checked="checked"{/if} /></td>
	<td class="OptionLabel"><label for="posted_data_by_company">{$lng.lbl_company}</label></td>
</tr>
</table>
	</td>
</tr>

{if $subsalesmans}
<tr>
    <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_sales_manager}:</td>
    <td width="10" height="10"><font class="CustomerMessage"></font></td>
    <td height="10" width="80%">
    <select name="posted_data[subsalesman]">
    <option value="">{$lng.lbl_myself}</option>
    {foreach from=$subsalesmans item=subsalesman}
    <option value="{$subsalesman.customer_id}" {if $search_prefilled.subsalesman eq $subsalesman.customer_id}selected{/if}>{$subsalesman.firstname} {$subsalesman.lastname}</option>
    {/foreach}
    </select>
    &nbsp;
    </td>
</tr>
{/if}

</table>

<br />

{include file="main/visiblebox_link.tpl" mark="1" title=$lng.lbl_advanced_search_options}

<br />

<table cellpadding="4" cellspacing="0" width="100%" style="display: none;" id="box1">

<tr>
	<td colspan="3"><br />{include file="common/subheader.tpl" title=$lng.lbl_advanced_search_options}</td>
</tr>

<tr class="TableSubHead">
    <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_user_type}:</td>
    <td width="10" height="10"><font class="CustomerMessage"></font></td>
    <td height="10">
    <select name="posted_data[membershipid]">
        <option value="">{$lng.lbl_all}</option>
{foreach from=$memberships item=v key=k}
        <option value="C-{$v.membershipid}"{if $search_prefilled.membershipid eq $v.membershipid} selected="selected"{/if}>{$v.membership}</option>
{/foreach}
    </select>
    </td>
</tr>

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_by_address}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td>
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="radio" id="address_type_null" name="posted_data[address_type]" value=""{if $search_prefilled eq "" or $search_prefilled.address_type eq ""} checked="checked"{/if} onclick="javascript:managedate('address',true)" /></td>
	<td class="OptionLabel"><label for="address_type_null">{$lng.lbl_ignore_address}</label></td>

	<td width="5"><input type="radio" id="address_type_B" name="posted_data[address_type]" value="B"{if $search_prefilled.address_type eq "B"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
	<td class="OptionLabel"><label for="address_type_B">{$lng.lbl_billing}</label></td>

	<td width="5"><input type="radio" id="address_type_S" name="posted_data[address_type]" value="S"{if $search_prefilled.address_type eq "S"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
	<td class="OptionLabel"><label for="address_type_S">{$lng.lbl_shipping}</label></td>

	<td width="5"><input type="radio" id="address_type_both" name="posted_data[address_type]" value="Both"{if $search_prefilled.address_type eq "Both"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
	<td class="OptionLabel"><label for="address_type_both">{$lng.lbl_both}</label></td>
</tr>
</table>
	</td>
</tr>

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_city}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
	<input type="text" maxlength="64" name="posted_data[city]" value="{$search_prefilled.city}" style="width:70%" />
	</td>
</tr>

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_state}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
	{include file="main/states.tpl" states=$states name="posted_data[state]" default=$search_prefilled.state required="N" style="style='width:70%'"}
	</td>
</tr>

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_country}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
	<select name="posted_data[country]" style="width:70%">
		<option value="">[{$lng.lbl_please_select_one}]</option>
{section name=country_idx loop=$countries}
		<option value="{$countries[country_idx].country_code}"{if $search_prefilled.country eq $countries[country_idx].country_code} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
	</select>
	</td>
</tr>

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_zipcode}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
	<input type="text" maxlength="16" name="posted_data[zipcode]" value="{$search_prefilled.zipcode}" style="width:70%" />
	</td>
</tr>

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_phone}/{$lng.lbl_fax}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
	<input type="text" maxlength="25" name="posted_data[phone]" value="{$search_prefilled.phone}" style="width:70%" />
	</td>
</tr>

<tr>
	<td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_web_site}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10" width="80%">
	<input type="text" maxlength="128" name="posted_data[url]" value="{$search_prefilled.url}" style="width:70%" />
	</td>
</tr>

<tr class="TableSubHead">
	<td height="10" class="FormButton">{$lng.lbl_search_for_users_that_is}:</td>
	<td height="10"></td>
	<td height="10">
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="checkbox" id="posted_data_registration_date" name="posted_data[registration_date]" value="Y"{if $search_prefilled.registration_date ne ""} checked="checked"{/if} onclick="javascript: managedate('date_type')" /></td>
	<td class="OptionLabel"><label for="posted_data_registration_date">{$lng.lbl_registered}</label></td>

	<td width="5"><input type="checkbox" id="posted_data_last_login_date" name="posted_data[last_login_date]" value="Y"{if $search_prefilled.last_login_date ne ""} checked="checked"{/if} onclick="javascript:managedate('date_type')" /></td>
	<td class="OptionLabel"><label for="posted_data_last_login_date">{$lng.lbl_last_logged_in}</label></td>
</tr>
</table>
	</td>
</tr>

<tr class="TableSubHead">
	<td class="FormButton" nowrap="nowrap">{$lng.lbl_during_date_period}:</td>
	<td width="10">&nbsp;</td>
	<td>
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input type="radio" id="date_period_M" name="posted_data[date_period]" value="M"{if $search_prefilled.date_period eq "" or $search_prefilled.date_period eq "M"} checked="checked"{/if} onclick="javascript:date_selected='M';managedate('date',true)" /></td>
	<td class="OptionLabel"><label for="date_period_M">{$lng.lbl_this_month}</label></td>

	<td width="5"><input type="radio" id="date_period_W" name="posted_data[date_period]" value="W"{if $search_prefilled.date_period eq "W"} checked="checked"{/if} onclick="javascript:date_selected='W';managedate('date',true)" /></td>
	<td class="OptionLabel"><label for="date_period_W">{$lng.lbl_this_week}</label></td>

	<td width="5"><input type="radio" id="date_period_D" name="posted_data[date_period]" value="D"{if $search_prefilled.date_period eq "D"} checked="checked"{/if} onclick="javascript:date_selected='D';managedate('date',true)" /></td>
	<td class="OptionLabel"><label for="date_period_D">{$lng.lbl_today}</label></td>
</tr>
<tr>
	<td width="5"><input type="radio" id="date_period_C" name="posted_data[date_period]" value="C"{if $search_prefilled.date_period eq "C"} checked="checked"{/if} onclick="javascript:date_selected='C';managedate('date',false)" /></td>
	<td colspan="7" class="OptionLabel"><label for="date_period_C">{$lng.lbl_specify_period_below}</label></td>
</tr>
</table>
	</td>
</tr>

<tr class="TableSubHead">
	<td class="FormButton" align="right" nowrap="nowrap">{$lng.lbl_from}:</td>
	<td width="10">&nbsp;</td>
	<td>
	{html_select_date prefix="Start" time=$search_prefilled.start_date start_year=$config.Company.start_year end_year=$config.Company.end_year}
	</td>
</tr>

<tr class="TableSubHead">
	<td class="FormButton" align="right" nowrap="nowrap">{$lng.lbl_through}:</td>
	<td width="10">&nbsp;</td>
	<td>
	{html_select_date prefix="End" time=$search_prefilled.end_date start_year=$config.Company.start_year end_year=$config.Company.end_year display_days=yes}
	</td>
</tr>


<tr>
	<td colspan="3"><br />
<script type="text/javascript" language="JavaScript 1.2">
<!--
{if $search_prefilled eq "" or $search_prefilled.address_type eq ""}
managedate('address',true);
{/if}
managedate('date_type');
{if ($search_prefilled.registration_date ne "" or $search_prefilled.last_login_date ne "") and $search_prefilled.date_period ne "C"}
managedate('date', true);
{/if}
-->
</script>
	</td>
</tr>

<tr>
	<td colspan="2">&nbsp;</td>
	<td>
	<input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
	&nbsp;&nbsp;&nbsp;
	<input type="button" value="{$lng.lbl_reset|strip_tags:false|escape}" onclick="javascript: reset_form('searchform', searchform_def);" /></td>
</tr>

</table>

	</td>
</tr>

</table>
</form>

{if $search_prefilled.need_advanced_options}
<script type="text/javascript" language="JavaScript 1.2">
<!--
visibleBox('1');
-->
</script>
{/if}

{/capture}
{include file="common/section.tpl" title=$lng.lbl_search_for_user content=$smarty.capture.section extra='width="100%"'}

<br />

<!-- SEARCH FORM DIALOG END -->

{/if}

<!-- SEARCH RESULTS SUMMARY -->

<a name="results"></a>

{if $mode eq "search"}
{include file="common/navigation_counter.tpl"}
{/if}

{if $mode eq "search" and $users ne ""}

<!-- SEARCH RESULTS START -->

<br /><br />

{capture name=section}

<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_search_again href="index.php?target=users"}</div>

{include file="common/navigation.tpl"}

<table cellpadding="2" cellspacing="1" width="100%">
<tr class="TableHead">
	<td>{if $search_prefilled.sort_field eq "email"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target=users&mode=search{$pagestr|amp}&amp;sort=email">{$lng.lbl_email}</a></td>
	<td>{if $search_prefilled.sort_field eq "name"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target=users&mode=search{$pagestr|amp}&amp;sort=name">{$lng.lbl_name}</a></td>
	<td>{if $search_prefilled.sort_field eq "last_login"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target=users&mode=search{$pagestr|amp}&amp;sort=last_login">{$lng.lbl_last_logged_in}</a></td>
</tr>

{section name=cat_num loop=$users}
<tr{cycle values=', class="TableSubHead"'}>
	<td><a href="index.php?target=user&user={$users[cat_num].customer_id|escape:"url"}" title="{$lng.lbl_modify_profile|escape}">{$users[cat_num].email}</a></td>
	<td><a href="index.php?target=user&user={$users[cat_num].customer_id|escape:"url"}" title="{$lng.lbl_modify_profile|escape}"><font class="ItemsList">{if $users[cat_num].usertype eq 'R'}{$users[cat_num].company}{else}{$users[cat_num].firstname} {$users[cat_num].lastname}{/if}</font></a> / {$users[cat_num].email}</td>
	<td nowrap="nowrap">{if ($users[cat_num].last_login ne 0)}{$users[cat_num].last_login|date_format:$config.Appearance.datetime_format}{else}{$lng.lbl_never_logged_in}{/if}</td>
</tr>
{/section}
</table>

{include file="common/navigation.tpl"}

{/capture}
{include file="common/section.tpl" title=$lng.lbl_search_results content=$smarty.capture.section extra='width="100%"'}

<!-- SEARCH RESULTS START -->

{/if}

<br />

