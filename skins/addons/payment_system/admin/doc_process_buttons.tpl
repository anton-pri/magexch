{tunnel func='cw_payment_is_authorized' assign='can_be_captured' via='cw_call' param1=$doc}
{if $can_be_captured}
<div class="product_buttons bottom">
 {include file='buttons/button.tpl' href="index.php?target=`$current_target`&amp;mode=capture&amp;doc_id=`$doc_id`" button_title='Capture'}
 {include file='buttons/button.tpl' href="index.php?target=`$current_target`&amp;mode=void&amp;doc_id=`$doc_id`" button_title='Void'}
</div>
{/if}
