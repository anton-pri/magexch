<div align="right">{include file='buttons/button.tpl' button_title=$lng.lbl_go_back href="javascript: history.go(-1);"}</div>

<br />

{$lng.txt_shipping_labels_help}
<br /><br />
<form action="index.php?target=generator" method="post" name="orders_form">

<script type="text/javascript">
<!--
{literal}
function openWindow() {
var x, str;
	if(checkboxes.length == 0)
		return false;

	str = '';
	for(x = 0; x < checkboxes.length; x++) {
		if(document.forms['orders_form'].elements[checkboxes[x]].checked)
			str = str+"&"+checkboxes[x]+"=Y";
	}
	window.open('generator.php?mode=get_label'+str,'SLabels','width=800,height=450,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=yes,location=no,direction=no');
}

{/literal}
-->
</script>

<table class="header" width="100%">
<tr>
	<th width="10"><input type="hidden" name="update" value="N"><input type='checkbox' class='select_all' class_to_select='order_item' /></th>
	<th width="10%">{$lng.lbl_order}</th>
	<th width="15%">{$lng.lbl_customer}</th>
	<th width="15%">{$lng.lbl_date}</th>
	<th width="30%">{$lng.lbl_shipping_method}</th>
	<th width="30%">{$lng.lbl_shipping_label}</th>
</tr>
{foreach from=$orders item=v}
<tr {cycle values=", class='cycle'"}>
	<td><input type="checkbox" name="doc_ids[{$v.doc_id}]" class="order_item" /><input type="hidden" name="doc_ids_all[{$v.doc_id}]" value="{$v.doc_id}"></td>
	<td align="center"><a href="index.php?target=docs_O&doc_id={$v.doc_id}" border="0">#{$v.display_id}</a></td>
	<td align="center">{$v.customer_id}</td>
	<td align="center">{$v.date|date_format:$config.Appearance.date_format}</td>
	<td align="center">{$v.shipping|trademark|default:$lng.txt_not_available}</td>
	<td align="center">
	{if $v.sl_type eq 'D' || $v.sl_type eq 'I'} 
	<a href="index.php?target=slabel&doc_id={$v.doc_id}">{$lng.lbl_download}</a></td>
	{elseif $v.sl_type eq 'E'}
		<b>{$lng.lbl_error}:</b> {$v.shipping_label_error}	
	{elseif $v.sl_type ne 'E' && $v.sl_type ne 'D' && $v.sl_type ne 'I'}
	{$lng.txt_not_available}
	{/if}

	</td>
</tr>
{/foreach}
{if $is_ups_exists}
<tr>
	<td colspan="6">
		<hr />
	</td>
</tr>
<tr>
	<td colspan="5">
	</td>
	<td align="center">
		<a href="index.php?target=slabel&doc_id=ups">{$lng.lbl_all_ups_labels}</a>
	</td>
</tr>
{/if}
</table>

<br />
<br />

{$lng.txt_shipping_labels_note}

<br />
<br />

<input type="button" value="{$lng.lbl_update_shipping_labels}" onclick="javascript: if (checkMarks(this.form, new RegExp('doc_ids\[[0-9]+\]', 'gi'))) {ldelim} document.orders_form.action='index.php?target=generator'; this.form.update.value='Y'; cw_submit_form('orders_form', ''); {rdelim}"/>
</form>
