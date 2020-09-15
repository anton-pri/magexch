<div id="customer_orders">
{assign var='total' value=0.00}
{assign var='total_paid' value=0.00}
<script>
<!--
var txt_delete_selected_orders_warning = "{$lng.txt_delete_selected_orders_warning|escape:javascript|strip_tags}";
-->
</script>

{if $orders}

<script type="text/javascript">
{literal}
function magexch_feedback_popup(seller_id, doc_id) {

//    alert(" "+seller_id+" "+customer_id);
    var form = 'process_order_form';
    var form_obj =  $('form[name='+form+']');
    if (form_obj) {
        if (!form_obj.attr('id')) {
            form_obj.attr('id',form);
        }
        form_obj.attr('blockUI',form_obj.attr('id'));
        document.process_order_form.order_seller_id.value = seller_id;
        document.process_order_form.feedback_order_id.value = doc_id;   
    }

    // Create popup if it is not created yet
    // Server response will use it for reply
    var popup = $('#seller_feedback_popup');
    if (popup.length == 0) {
        popup = $('<div id="seller_feedback_popup"></div>');
        $('body').append(popup);
    }

    submitFormAjax.apply(form_obj,[form]);

}
{/literal}
</script>

<form action="index.php?target={$current_target}" method="post" name="process_order_form">
<input type='hidden' name='order_seller_id' value='' />
<input type='hidden' name='feedback_order_id' value='' />
<input type='hidden' name='order_customer_id' value='{$customer_id}' />
<input type="hidden" name="action" value="feedback_display" />

<!-- cw@orders_list_table [ -->

<div class="box orders" id="magexch_box_orders">

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr style="background-color: #f2f2f2;">
	<th width="20%">{if $search_prefilled.sort_field eq "doc_id"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=doc_id&amp;sort_direction={if $search_prefilled.sort_field eq 'doc_id'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_order}</a></th>
	<th>{if $search_prefilled.sort_field eq "status"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=status&amp;sort_direction={if $search_prefilled.sort_field eq 'status'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_status}</a></th>
{*
    <th>
        {if $search_prefilled.sort_field eq 'seller_info'}{include file='buttons/sort_pointer.tpl' dir=$search_prefilled.sort_direction}&nbsp;{/if}
        <a href="index.php?target={$current_target}&amp;mode=search&amp;sort=seller_info&amp;sort_direction={if $search_prefilled.sort_field eq 'seller_info'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{lng name="lbl_seller_info"}</a>
    </th>
*}
	<th>{if $search_prefilled.sort_field eq "date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=date&amp;sort_direction={if $search_prefilled.sort_field eq 'date'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_date}</a></th>

    {include file='main/docs/extras_title.tpl' is_title=true orders_list='Y'}

    <th>{if $search_prefilled.sort_field eq "total"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=total&amp;sort_direction={if $search_prefilled.sort_field eq 'total'}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_total}</a></th>
{*
    <th>&nbsp;</th>
*}
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

	<td><a href="index.php?target={$current_target}&mode=details&doc_id={$order.doc_id}">#{$order.display_id}</a></td>
	<td>
        <!-- cw@orders_list_status [ -->
        <div>
            <b>{include file="main/select/doc_status.tpl" status=$order.status mode="static"}</b>
        </div>
        <!-- cw@orders_list_status ] -->
	</td>
{*
    <td align="center">
        {include file="main/seller_info.tpl" seller_customer_id=$order.warehouse_customer_id} 
    </td>
*}

    <td nowrap="nowrap">{$order.date|date_format:$config.Appearance.datetime_format}</td>

    {include file='main/docs/extras.tpl' extras=$order.extras order=$order orders_list="Y"}

    <td nowrap="nowrap" align="right">
	{include file='common/currency.tpl' value=$order.total}
	</td>
{*
    <td>
        &nbsp;{if !in_array($order.status, array('D','F','I'))}{include file="main/seller_feedback_button.tpl" seller_customer_id=$order.warehouse_customer_id customer_id=$order.customer_id doc_id=$order.doc_id}{/if}
    </td>
*}
</tr>
{/foreach}
</tbody>
</table>
</div>
<!-- cw@orders_list_table ] -->

<!-- cw@orders_list_total [ -->
<div class="text-right" style="margin-top:14px;">
	<label>{$lng.lbl_gross_total}:<label>
    {include file='common/currency.tpl' value=$total}
</div><br/>
<!-- cw@orders_list_total ] -->


</form>
{/if}
</div>
