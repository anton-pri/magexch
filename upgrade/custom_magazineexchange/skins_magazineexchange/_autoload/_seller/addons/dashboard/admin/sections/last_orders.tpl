<div class='dashboard_last_orders'>
<table class="table remove-margin-b font-s13">
{foreach from=$orders item=order}
        <tr class='{cycle values=',cycle'}'>
            <td class="font-w600"><a href="index.php?target=docs_O&doc_id={$order.id}">{$order.name|truncate:15}</a></td>
            <td class="text-right hidden-xs"><a class="order_{$order.status} order_status_link" href="index.php?target=docs_O&doc_id={$order.id}">#{$order.display_id}</a></td>
            <td class="text-right font-w600 text-green"><nobr>+{include file='common/currency.tpl' value=$order.total}</nobr></td>
        </tr>
{/foreach}
</table>
</div>

{include file='admin/buttons/button.tpl' button_title=$lng.lbl_admin_more href='index.php?target=docs_O&mode=search' style="btn-green"}
{*
<div class="block-button text-right block-content block-content-full"><a href="{$catalogs.admin}/index.php?target=docs_O&mode=search" class="btn btn-minw btn-info">{$lng.lbl_admin_more}</a></div>
*}
