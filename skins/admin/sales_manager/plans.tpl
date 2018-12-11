<div class="section_title">{$lng.txt_affiliate_plan_note}</div>

<form action="index.php?target={$current_target}" name="plans_form" method="post">
<input type="hidden" name="action" value="update" />

<table class="header" width="100%">
<tr>
	<th width="5">{$lng.lbl_del}</th>
	<th width="70%">{$lng.lbl_plan_title}</th>
	<th width="10%">{$lng.lbl_active}</th>
    <th>&nbsp;</th>
</tr>
{assign var="is_first" value=true}
{if $salesman_plans}
{foreach from=$salesman_plans item=p}
<tr{cycle values=', class="cycle"'}>
	<td><input type="checkbox" name="plans[{$p.plan_id}][del]" value="1" /></td>
	<td><input type="text" name="plans[{$p.plan_id}][title]" size="45" maxlength="64" value="{$p.title}" /></td>
	<td>{include file='main/select/yes_no.tpl' name="plans[`$p.plan_id`][status]" value=$p.status}</td>
    <td><a href="index.php?target={$current_target}&mode=edit&plan_id={$p.plan_id}">{$lng.lbl_modify}</a></td>
</tr>
{/foreach}
{else}
<tr>
	<td colspan="4" align="center">{$lng.lbl_no_affiliate_plans_defined}</td>
</tr>
{/if}
{if $accl.__1101}
<tr>
    <td colspan="4">
{include file="common/subheader.tpl" title=$lng.lbl_add_affiliate_plan}</td></tr>
    </td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="text" name="plans[0][title]" size="45" maxlength="64" /></td>
	<td>{include file='main/select/yes_no.tpl' name="plans[0][status]"}</td>
    <td>&nbsp;</td>
</tr>
{/if}
</table>

{include file='buttons/button.tpl' button_title=$lng.lbl_update_delete href="javascript: cw_submit_form('plans_form')" acl='__1101'}

</form>
