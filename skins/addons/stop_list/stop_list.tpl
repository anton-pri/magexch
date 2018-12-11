{include file='common/page_title.tpl' title=$lng.lbl_stop_list}
{$lng.txt_stop_list_note}<br /><br />

<!-- IN THIS SECTION -->



<!-- IN THIS SECTION -->
<br />

{if $mode ne 'add'} 
{capture name=section}

<form action="index.php?target=stop_list" method="post" name="stoplistform">
<input type="hidden" name="action" value="" />

<table class="header" width="100%">
<tr>
	<th width="10"><input type='checkbox' class='select_all' class_to_select='stoplist_item' /></th>
	<th>{$lng.lbl_ip_address}</th>
	<th>{$lng.lbl_reason}</th>
	<th>{$lng.lbl_status}</th>
	<th>{$lng.lbl_date}</th>
</tr>
{foreach from=$stop_list item=v}
<tr{cycle name="classes" values=', class="TableSubHead"'}>
	<td align="center"><input type="checkbox" name="to_delete[{$v.ip}]" value="Y" class="stoplist_item" /></td>
	<td><a href="index.php?target=stop_list&mode=add&ipid={$v.ipid}">{$v.ip}</a></td>
	<td>{$v.reason_text}</td>
	<td align="center">{if $v.ip_type eq 'T'}{$lng.lbl_trusted}{else}{$lng.lbl_blocked}{/if}</td>
	<td>{$v.date|date_format:$config.Appearance.datetime_format}</td>
</tr>
{foreachelse}
<tr>
	<td colspan="4" align="center">{$lng.lbl_stop_list_empty}</td>
</tr>
{/foreach}
{if $stop_list ne ''}
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan="4"><input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="document.stoplistform.mode.value='delete'; document.stoplistform.submit();" /></td>
</tr>
{/if}
</form>
</table>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_stop_list extra='width="100%"'}

{else}

<script type="text/javascript">
<!--
var lbl_octet_has_wrong_format = "{$lng.lbl_octet_X_has_wrong_format|strip_tags|escape:javascript}";
{literal}
function checkIP(obj, idx) {
	if(obj.value != '*') {
		var i = parseInt(obj.value);
		if(i > 255 || i < 0 || (idx == 1 && i == 0)) {
			alert(substitute(lbl_octet_has_wrong_format, "X", idx));
			return false;
		}
	}
	return true;
}

function checkAllIP() {
	return (checkIP(document.getElementById("o0"), 1) &&
		checkIP(document.getElementById("o1"), 2) &&
		checkIP(document.getElementById("o2"), 3) &&
		checkIP(document.getElementById("o3"), 4)
	);
}
{/literal}
-->
</script>
{capture name=section}
<b>{$lng.lbl_note}:</b> {$lng.txt_stop_list_add_ip_address_note}<br /><br />
<table border="0">
<form action="index.php?target=stop_list" method="post" name="stoplistform" onsubmit="javascript: return checkAllIP();">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="ipid" value="{$ip.ipid}" />
<tr>
	<td>{$lng.lbl_ip_address}:</td>
	<td>
	<input id="o0"type="text" maxlength="3" size="3" name="octet[0]" value="{if $ip.octet1 eq -1}*{else}{$ip.octet1}{/if}" onchange="javascript: checkIP(this, 1);" />.
	<input id="o1" type="text" maxlength="3" size="3" name="octet[1]" value="{if $ip.octet2 eq -1}*{else}{$ip.octet2}{/if}" onchange="javascript: checkIP(this, 2);" />.
	<input id="o2" type="text" maxlength="3" size="3" name="octet[2]" value="{if $ip.octet3 eq -1}*{else}{$ip.octet3}{/if}" onchange="javascript: checkIP(this, 3);" />.
	<input id="o3" type="text" maxlength="3" size="3" name="octet[3]" value="{if $ip.octet4 eq -1}*{else}{$ip.octet4}{/if}" onchange="javascript: checkIP(this, 4);" />
	</td>
</tr>
<tr>
	<td>{$lng.lbl_status}:</td>
	<td><select name="ip_type">
	<option value="B"{if $ip.ip_type ne 'T'} selected="selected"{/if}>{$lng.lbl_blocked}</option>
	<option value="T"{if $ip.ip_type eq 'T'} selected="selected"{/if}>{$lng.lbl_trusted}</option>
	</select></td>
</tr>
<tr>
    <td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="{if $ip.ipid  gt 0}{$lng.lbl_update|strip_tags:false|escape}{else}{$lng.lbl_add|strip_tags:false|escape}{/if}" /></td>
</tr>
</table>
</form>
{if $ip.ipid > 0}
{assign var="dialog_title" value=$lng.lbl_update_ip_address}
{else}
{assign var="dialog_title" value=$lng.lbl_add_ip_address}
{/if}
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$dialog_title extra='width="100%"'}

{/if}
