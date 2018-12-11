{include file='common/page_title.tpl' title=$lng.lbl_tax_rates}

{$lng.txt_tax_rates_general_note}

<br /><br />

<!-- IN THIS SECTION -->



<!-- IN THIS SECTION -->

<br />

{capture name=section}

<form action="index.php?target=taxes" method="post" name="taxesform">
<input type="hidden" name="action" value="apply" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
	<td width="5%"><input type='checkbox' class='select_all' class_to_select='wtaxes_item' /></td>
	<td width="65%">{$lng.lbl_tax_name}</td>
	<td width="30%" align="center">{$lng.lbl_status}</td>
</tr>

{if $taxes}

{section name=tax loop=$taxes}

<tr {cycle values=", class='cycle'"}
	<td><input type="checkbox" name="to_delete[{$taxes[tax].tax_id}]" class="wtaxes_item" /></td>
	<td>
<a href="index.php?target=taxes&tax_id={$taxes[tax].tax_id}">{$taxes[tax].tax_name}</a>
({$lng.txt_N_rates_defined|substitute:"rates":$taxes[tax].rates_count})
	</td>
	<td align="center">{if $taxes[tax].active eq "Y"}{$lng.lbl_enabled}{else}{$lng.lbl_disabled}{/if}</td>
</tr>

{/section}
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan="3"><input type="button" value="{$lng.lbl_apply_selected_taxes_to_all_products|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('to_delete\[[0-9]+\]', 'gi'))) cw_submit_form(this, 'apply');"/></td>
</tr>
{else}

<tr>
	<td colspan="3" align="center">{$lng.txt_no_taxes_defined}</td>
</tr>

{/if}

</table>
</form>

<br /><br />

{/capture}
{include file="common/section.tpl" title=$lng.lbl_taxes content=$smarty.capture.section extra='width="100%"'}

