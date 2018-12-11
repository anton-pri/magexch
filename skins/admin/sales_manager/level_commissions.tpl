<div class="dialog_title">{$lng.txt_salesmanship_commissions_note}</div>

<form action="index.php?target={$current_target}" method="post" name="levels_form">
<input type="hidden" name="action" value="edit" />

<table class="header">
<tr>
	<th width="40">{$lng.lbl_level}</th>
	<th width="100">{$lng.lbl_commission}</th>
</tr>
{foreach from=$levels item=v key=k}
<tr>
    <td>{$k}</td>
    <td><input size="6" type="text" name="level[{$k}]" value="{$v.commission|formatprice|default:$zero}" />%</td>
</tr>
{/foreach}
</table>
{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('levels_form')"}

</form>
