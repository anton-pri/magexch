{if $include_feedback_popup_js_code eq 'Y'}
<script type="text/javascript">
{literal}
function magexch_feedback_popup(seller_id, doc_id) {

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
<form action="index.php?target=docs_O" method="post" name="process_order_form">
<input type='hidden' name='order_seller_id' value='' />
<input type='hidden' name='feedback_order_id' value='' />
<input type='hidden' name='order_customer_id' value='{$customer_id}' />
<input type="hidden" name="action" value="feedback_display" />
</form>
{/if}
{if $seller_customer_id gt 0}
{tunnel func='magexch_seller_feedback_customer_info' via='cw_call' param1=$seller_customer_id param2=$customer_id param3=$doc_id assign='seller_feedback_customer_info'}
{if $seller_feedback_customer_info.feedback_left}
    {include file='buttons/button.tpl' button_title=$lng.lbl_feedback_left href="javascript: magexch_feedback_popup(`$seller_customer_id`, `$doc_id`);" style="button" class="button-grey"}
{else}
    {include file='buttons/button.tpl' button_title=$lng.lbl_leave_feedback href="javascript: magexch_feedback_popup(`$seller_customer_id`, `$doc_id`);" style="button"}
{/if}
{/if}
