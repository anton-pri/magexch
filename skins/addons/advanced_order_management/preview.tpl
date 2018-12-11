<div class="edit_order_controls">
{include file='admin/buttons/button.tpl' href="index.php?target=docs_`$order.type[0]`&doc_id=`$order.doc_id`&mode=edit&action=save" button_title=$lng.lbl_save style="btn-green push-5-r"}
{include file='admin/buttons/button.tpl' href="index.php?target=docs_`$order.type[0]`&doc_id=`$order.doc_id`&mode=edit&action=cancel" button_title=$lng.lbl_reset style="btn-danger push-5-r"}
{include file='admin/buttons/button.tpl' href="index.php?target=docs_`$order.type[0]`&doc_id=`$order.doc_id`" button_title=$lng.lbl_exit style="btn-warning push-5-r"}
<div class="clear"></div>
</div>

{include file='admin/docs/doc_layout.tpl' doc=$order}
