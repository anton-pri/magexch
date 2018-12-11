<p />
{capture name=section}
<form name="searchgiftregform" action="index.php?target=giftregs" method="post">

<table>
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_giftreg_creator_name}:</td>
	<td width="10" height="10"></td>
	<td height="10"><input type="text" name="post_data[name]" size="35" value="{$search_data.name|escape:"html"}" /></td>
</tr>
<tr> 
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_giftreg_creator_email}:</td>
	<td width="10" height="10"></td>
	<td height="10"><input type="text" name="post_data[email]" size="35" value="{$search_data.email|escape:"html"}" /></td>
</tr>
<tr>
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_keyword}:</td>
	<td width="10" height="10"></td>
	<td height="10"><input type="text" name="post_data[substring]" size="50" value="{$search_data.substring|escape:"html"}" /></td>
</tr>
<tr>
	<td height="10">&nbsp;</td>
	<td width="10" height="10"></td>
	<td height="10" nowrap="nowrap" valign="top">

<table cellspacing="0" cellpadding="0">
<tr>
	<td><input type="checkbox" id="inc_desciption" name="post_data[inc_desciption]" value="Y"{if $search_data.inc_desciption eq "Y"}checked{/if} /></td>
	<td> <label for="inc_desciption">{$lng.lbl_search_description}</label></td>
</tr>
</table>
	</td>
</tr>
<tr> 
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_giftreg_event_status}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10">
	<select name="post_data[status]">
		<option value="">{$lng.lbl_all}</option>
		<option value="P"{if $search_data.status eq "P"} selected="selected"{/if}>{$lng.lbl_private}</option>
		<option value="G"{if $search_data.status eq "G"} selected="selected"{/if}>{$lng.lbl_public}</option>
	</select>
	</td>
</tr>
{math equation="x+5" x=$config.Company.end_year assign="endyear"}
<tr> 
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_giftreg_event_date_from}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10">
{html_select_date prefix="Start" time=$search_data.start_date start_year=$config.Company.start_year end_year=$endyear}
	</td>
</tr>
<tr> 
	<td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_giftreg_event_date_through}:</td>
	<td width="10" height="10"><font class="CustomerMessage"></font></td>
	<td height="10">
{html_select_date prefix="End" time=$search_data.end_date start_year=$config.Company.start_year end_year=$endyear display_days=yes}
	</td>
</tr>

<tr> 
	<td width="78" class="FormButton">&nbsp;</td>
	<td width="10">&nbsp;</td>
	<td height="30"><input type="button" value="{$lng.lbl_search|strip_tags:false|escape}" OnClick="cw_submit_form(document.searchgiftregform);" /></td>
</tr>
</table>

</form>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_giftreg_search content=$smarty.capture.section extra='width="100%"'}

<p />
{if $smarty.get.mode eq "search"}
{$items_count} {$lng.lbl_events_found}
{/if}

{if $search_result ne ""}
<p />
{capture name=section}
{ include file="common/navigation.tpl" }
<table cellpadding="0" cellspacing="0" width="100%">
{section name=res loop=$search_result}
{if %res.first%}
<tr class="TableHead">
	<td colspan="5"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{/if}
<tr>
	<td colspan="5">&nbsp;</td>
</tr>
<tr>
	<td valign="middle">
<table cellspacing="0" cellpadding="0">
<tr><td>
{if $search_result[res].status eq "P"}
<img src="{$ImagesDir}/private.gif" width="19" height="19" alt="{$lng.lbl_private|escape}" />
{else}
<img src="{$ImagesDir}/public.gif" width="19" height="19" alt="{$lng.lbl_public|escape}}" />
{/if}
</td>
<td><a href="index.php?target=giftregs&eventid={$search_result[res].event_id}" title="{$lng.lbl_event_info|escape}"><b>{$search_result[res].event_date|date_format:"%B %e, %Y"} - {$search_result[res].title}</b></a></td>
</tr>
</table>
  </td>
  <td><span title="{$lng.lbl_giftreg_creator_name|escape}"><b>{$search_result[res].firstname} {$search_result[res].lastname}</b></span></td>
  <td align="right"><a href="index.php?target=giftregs&eventid={$search_result[res].event_id}" title="{$lng.lbl_wish_list|escape}"><b>{$search_result[res].products} {$lng.lbl_products}</b></a></td>
</tr>
{if $search_result[res].description ne ""}
<tr>
	<td colspan="5">{$search_result[res].description}</td>
</tr>
{/if}
<tr>
	<td colspan="5">&nbsp;</td>
</tr>
<tr class="TableHead">
	<td colspan="5"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{/section}
</table>
<br />
{ include file="common/navigation.tpl" }
{/capture}
{include file="common/section.tpl" title=$lng.lbl_search_results content=$smarty.capture.section extra='width="100%"'}
{/if}
