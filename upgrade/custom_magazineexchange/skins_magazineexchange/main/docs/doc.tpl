{jstabs name='doc_O_info'}
default_tab={$js_tab|default:"order_details"}

[order_details]
title="{lng name="lbl_doc_info_O"}"
template="main/docs/doc_layout.tpl"

[status_seller_feedback]
title="{$lng.lbl_status_seller_feedback}"
template="main/seller_feedback.tpl"

{/jstabs}

<div id='customer_order'>
{include file='tabs/js_tabs.tpl'}
</div>

{*
<!-- cw@order_block [ -->
{include file='main/docs/doc_layout.tpl'}
<!-- cw@order_block ] -->
*}
