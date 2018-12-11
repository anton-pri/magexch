<div class="dialog_title">{$lng.txt_salesman_commissions_note}</div>
<script type="text/javascript" language="JavaScript 1.2">
<!--
var txt = '';
var lbl_all_salesmans = "{$lng.lbl_all_salesmans|escape:javascript}";
var txt_apply_aff_plan_to_salesmans = "{$lng.txt_apply_aff_plan_to_salesmans|escape:javascript}";

{literal}
function change_filter(obj) {
	if (!document.getElementById('salesman') || !document.getElementById('use_filter'))
		return false;

	document.getElementById('salesman').disabled = !document.getElementById('use_filter').checked;
	if (document.getElementById('use_filter').checked) {
		if (txt == lbl_all_salesmans)
			txt = '';
		if (document.getElementById('salesman').value == lbl_all_salesmans)
			document.getElementById('salesman').value = txt;

	} else {
		txt = document.getElementById('salesman').value;
		document.getElementById('salesman').value = lbl_all_salesmans;
	}
}
{/literal}
-->
</script>

<form action="index.php?target=salesman_commissions" method="post" name="search_form">
<input type="hidden" name="action" value="go" />

<div class="input_field_0">
    <label>{$lng.lbl_plan}</label>
	<select name="pc">
		<option value="">{$lng.lbl_select_affiliate_plan}</option>
{foreach from=$salesman_plans item=plan}
		<option value="{$plan.plan_id}">{$plan.title}</option>
{/foreach}
	</select>
</div> 
<div class="input_field_0">
	<label>{$lng.lbl_salesman}</label>
	<input type="text" value="{$salesman}" name="salesman" id="salesman" />
	<input type="checkbox" id="use_filter" name="use_filter" value="Y"{if $use_filter eq 'Y'} checked="checked"{/if} onclick="javascript: change_filter();" />
	{$lng.lbl_use_filter}
</div>
{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript: if (confirm(txt_apply_aff_plan_to_salesmans)) cw_submit_form('search_form', 'apply_global');" acl='__1102'}

{include file='buttons/button.tpl' button_title=$lng.lbl_show href="javascript: cw_submit_form('search_form');"}
</form>

<script type="text/javascript" language="JavaScript 1.2">
<!--
change_filter();
-->
</script>

{if $mode eq "go"}
{capture name=section}
{if $salesman_info eq ""}
{$lng.lbl_no_salesmans}
{else}
<form method="post" action="index.php?target=salesman_commissions" name="salesman_form">
<input type="hidden" name="action" value="apply" />
<input type="hidden" name="salesman" value="{$salesman}" />
<input type="hidden" name="page" value="{$smarty.get.page|escape:"html"}" />

<table class="header" width="100%">
<tr>
	<th width="80%">{$lng.lbl_title}</th>
	<th width="20%">{$lng.lbl_affiliate_plan}</th>
</tr>
{foreach from=$salesman_info item=p}
<tr{cycle values=', class="cycle"'}>
	<td><a href="index.php?target=user_B&mode=modify&user={$p.customer_id}">{$p.customer_id|user_title:'B'}</a></td>
	<td>
	<select name="plans[{$p.customer_id}]">
		<option value="">{$lng.lbl_no_plans_assigned}</option>
{foreach from=$salesman_plans item=plan}
		<option value="{$plan.plan_id}"{if $plan.plan_id eq $p.plan_id} selected="selected"{/if}>{$plan.title|escape}</option>
{/foreach}
	</select>
	</td>
</tr>
{/foreach}
</table>
{include file='buttons/button.tpl' button_title=$lng.lbl_apply href="javascript: cw_submit_form('salesman_form')" acl='__1102'}

</form>

{/if}
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_search_results}
{/if}
