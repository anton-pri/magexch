{assign var='total' value=0.00}
{assign var='total_paid' value=0.00}
<script>
<!--
var txt_delete_selected_orders_warning = "{$lng.txt_delete_selected_orders_warning|escape:javascript|strip_tags}";
-->
</script>

{if $orders}

<form action="index.php?target={$current_target}" method="post" name="process_order_form">
<div class="box orders">

<input type="hidden" name="action" value="" />

{assign var='colspan' value=6}

<table class="table table-striped dataTable" width="100%">
<thead>
<tr>
	<th width="5"><input type='checkbox' class='select_all' class_to_select='orders_item' /></th>
	<th width="5%">{if $search_prefilled.sort_field eq "doc_id"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=doc_id&amp;sort_direction={if $search_prefilled.sort_field eq 'doc_id'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">#</a></th>
	<th>{if $search_prefilled.sort_field eq "status"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=status&amp;sort_direction={if $search_prefilled.sort_field eq 'status'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_status}</a></th>
	{if $current_area eq 'A'}
		<th></th>
		<th></th>
	{/if}
	<th>
        {if $search_prefilled.sort_field eq 'customer'}{include file='buttons/sort_pointer.tpl' dir=$search_prefilled.sort_direction}&nbsp;{/if}
        <a href="index.php?target={$current_target}&amp;mode=search&amp;sort=customer&amp;sort_direction={if $search_prefilled.sort_field eq 'customer'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{lng name="lbl_order_customer_`$docs_type`"}</a>
    </th>
    {*
    {if $usertype eq "A"}
    {assign var="colspan" value=7}
        <th>{if $search_prefilled.sort_field eq "warehouse"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=warehouse&amp;sort_direction={if $search_prefilled.sort_field eq 'warehouse'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_warehouse}</a></th>
    {/if}
    *}
	<th>{if $search_prefilled.sort_field eq "date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=date&amp;sort_direction={if $search_prefilled.sort_field eq 'date'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_date}</a></th>

    {include file='main/docs/extras_title.tpl' is_title=true orders_list='Y'}

    <th class="text-right">{if $search_prefilled.sort_field eq "total"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=total&amp;sort_direction={if $search_prefilled.sort_field eq 'total'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_total}</a></th>
</tr>
</thead>
<tbody>
{foreach from=$orders item=order}

{math equation="x + ordertotal" x=$total ordertotal=$order.total assign="total"}
{if $order.status eq "P" or $order.status eq "C"}
{math equation="x + ordertotal" x=$total_paid ordertotal=$order.total assign="total_paid"}
{/if}

<tr {cycle values=", class='cycle'"} 
{tunnel func='cw_doc_get_order_status_color' status_code=$order.status assign='status_color'} 
{if $status_color ne ''}style="background-color: {$status_color}"{/if}>

	<td width="5"><input type="checkbox" name="doc_ids[{$order.doc_id}]" class="orders_item" /></td>
	<td><a href="index.php?target={$current_target}&mode=details&doc_id={$order.doc_id}">#{$order.display_id}</a></td>
	<td>

{if $current_target eq "docs_I"}
	{assign var='doc_status_tpl' value="doc_i_status"}
{else}
	{assign var='doc_status_tpl' value="doc_status"}
{/if}

{if $usertype eq "A" or $usertype eq "P"}
<input type="hidden" name="order_status_old[{$order.doc_id}]" value="{$order.status}" />
{include file="main/select/`$doc_status_tpl`.tpl" status=$order.status mode="select" name="order_status[`$order.doc_id`]" extra="class='short form-control'"}
{else}
<a href="index.php?target={$current_target}&mode=details&doc_id={$order.doc_id}"><b>{include file="main/select/`$doc_status_tpl`.tpl" status=$order.status mode="static"}</b></a>
{/if}
{if $addons.stop_list && $order.blocked  eq 'Y'}
<img src="{$ImagesDir}/no_ip.gif" style="vertical-align: middle;" alt="" />
{/if}
	</td>
	{if $current_area eq 'A'}
		<td>
			{if $order.customer_notes ne ''}
			<span class="order_tooltip" title="{$order.customer_notes}">
				<i class="si si-info fa-15x text-info"></i>
			</span>
			{/if}
		</td>
		<td>
			{if $order.notes ne ''}
			<span class="order_tooltip" title="{$order.notes}">
				<i class="si si-info fa-15x text-danger"></i>
			</span>
			{/if}
		</td>
	{/if}
	<td class="font-w600">
        {$order.customer_id|user_title:$order.usertype:$order.doc_id}
    </td>
{*
{if $usertype eq 'A'}
	<td>{$order.warehouse_title}</td>
{/if}
*}

    <td nowrap="nowrap"><a href="index.php?target={$current_target}&mode=details&doc_id={$order.doc_id}">{$order.date|date_format:$config.Appearance.datetime_format}</a></td>

    {include file='main/docs/extras.tpl' extras=$order.extras order=$order orders_list="Y"}

    <td nowrap="nowrap" align="right">
	<a href="index.php?target={$current_target}&mode=details&doc_id={$order.doc_id}">{include file='common/currency.tpl' value=$order.total}</a>
	</td>
</tr>

{/foreach}
<tbody>

</table>

<!-- cw@orders_list_total [ -->
<div class="text-right">
    <label>{$lng.lbl_gross_total}: <label>
    {include file='common/currency.tpl' value=$total}
</div>
<div class="text-right">
    <label>{$lng.lbl_total_paid}: <label>
    {include file='common/currency.tpl' value=$total_paid}
</div>
<!-- cw@orders_list_total ] -->


{if $usertype eq 'A'}
<script language="javascript">
{literal}
function cw_submit_form_check(mode, target) {
    if (checkMarks(document.process_order_form, new RegExp('doc_ids\[[0-9]+\]', 'gi'))) {
        if (target)
            document.process_order_form.target=target; 
        cw_submit_form(document.process_order_form, mode); 
        document.process_order_form.target=''; 
    }
}
$(document).ready(function() {
	$('.order_tooltip').each(function() {
		$(this).tooltip({
			'onCreate': function(ele, options) {
				options.openTrigger = 'hover';
				options.closeTrigger = 'hover';
				options.content = $(ele).attr("title").replace(/\n/g,"<br>");
				$(ele).attr("title", null);
			}
		});
	});
});
{/literal}
</script>
</div>
<div id="sticky_content" class="buttons form-group">
  {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('process_order_form', 'update');" button_title=$lng.lbl_update acl=$page_acl style="push-5-r btn-green"}
  {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form_check('print', 'invoices');" button_title=$lng.lbl_print_selected style="push-5-r btn-green"}
  {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('process_order_form', 'delete');" button_title=$lng.lbl_delete_selected acl=$page_acl style="push-5-r btn-danger"}
<!-- cw@orders_list_buttons -->
{if $usertype eq "A"}
  {if $current_target eq "docs_I"}
    {assign var='doc_status_tpl' value="doc_i_status"}
  {else}
    {assign var='doc_status_tpl' value="doc_status"}
  {/if}
  {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('process_order_form', 'mass_update');" button_title=$lng.lbl_update_statuses_to style="push-5-r btn-green"}
  {include file="main/select/`$doc_status_tpl`.tpl" mode="select" name="mass_update_order_status" status='C' extra="class='short' style='margin-top:12px'"}
{/if}

</div>

{/if}
</form>
{/if}
