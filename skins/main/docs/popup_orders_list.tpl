<script language="javascript">
{literal}
function cw_set_doc() {
    value = document.results_form.doc_id.value;
    if (value) {
{/literal}
        window.opener.document.getElementById('{$element_id}').value=value;
//        cw_submit_form(window.opener.document.{$target_form});
        window.close();
{literal}
    }
}
{/literal}
</script>

{if $orders}
<form action="index.php?target={$current_target}" method="post" name="results_form">
<input type="hidden" name="action" value="" />
<input type="hidden" name="doc_id" value="0">

<table class="header" width="100%">
<tr>
	<th width="5">&nbsp;</th>
	<th width="5%">{if $search_prefilled.sort_field eq "display_id"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=display_id">#</a></th>
	<th width="10%">{if $search_prefilled.sort_field eq "status"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=status">{$lng.lbl_status}</a></th>
	<th width="30%">
        {if $search_prefilled.sort_field eq 'customer'}{include file='buttons/sort_pointer.tpl' dir=$search_prefilled.sort_direction}&nbsp;{/if}
        <a href="index.php?target={$current_target}s&mode=search&amp;sort=customer">{lng name="lbl_order_customer_`$docs_type`"}</a>
    </th>
{if $usertype eq 'A'}
	<th width="20%">{if $search_prefilled.sort_field eq "warehouse"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=warehouse">{$lng.lbl_warehouse}</a></th>
{/if}
	<th width="20%">{if $search_prefilled.sort_field eq "date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=date">{$lng.lbl_date}</a></th>
	<th width="20%">{if $search_prefilled.sort_field eq "total"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="index.php?target={$current_target}&mode=search&amp;sort=total">{$lng.lbl_total}</a></th>
</tr>

{foreach from=$orders item=order}
<tr {cycle values=", class='cycle'"}>
    <td width="5"><input type="radio" name="doc_id_radio" value="{$order.doc_id}"{if $order.doc_id eq $doc_id} disabled="disabled"{/if} onchange="javascript:document.results_form.doc_id.value=this.value"/></td>
	<td>#{$order.display_id}</td>
	<td>{include file='main/select/doc_status.tpl' status=$order.status mode='static'}</td>
	<td>{$order.customer_id|user_title:$order.usertype:$order.doc_id}</td>
{if $usertype eq 'A'}
	<td>{$order.warehouse_title}</td>
{/if}
	<td>{$order.date|date_format:$config.Appearance.datetime_format}</a></td>
	<td nowrap="nowrap" align="right">{include file='common/currency.tpl' value=$order.total}</td>
</tr>
{/foreach}
</table>

{include file='buttons/button.tpl' href="javascript:cw_set_doc();" button_title=$lng.lbl_select}

</form>
{/if}
