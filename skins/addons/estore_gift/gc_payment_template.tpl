<script type="text/javascript" language="JavaScript 1.2">
<!--
var total = "{$cart.info.total}";
var giftcert_discount = "{$cart.info.giftcert_discount}";
var label_applied = "{$lng.lbl_l_applied}";
{literal}
$(document).ready(function() {
	// Show applied
	if (giftcert_discount && giftcert_discount > 0) {
		$("#giftcert_applied").css("display", "block");
	}

	// Disable field if total is 0
	if (total == 0) {
		$("#giftcert_form_gc_id").prop("disabled", "disabled");
		$("#giftcert_form_button").prop("href", "javascript: void(0);");
	}
});

// Ajax apply giftcert
function cw_gc_ajax(handler) {
	toggleSections(handler, false);

	$.ajax({
	    'type'		: 'post',
	    'url'		: 'index.php?target=cart&is_ajax=1',
	    'data'		: 'mode=checkout&action=apply_gc&gc_id=' + $("#giftcert_form_gc_id").val(),
	    'success'	: eval(handler),
	    'error'		: function() {alert('GC Error occured (debug: JS cw_submit_form_ajax)');},
	    'dataType'	: 'xml'
	});
}
{/literal}
-->
</script>

<div>
	<label style="float:left;padding-right: 10px;">{$lng.lbl_giftcert_ID}:</label>
	<input type="text" id="giftcert_form_gc_id" name="gc_id" size="30" value="" />
	<a id="giftcert_form_button" class="image-button" href="javascript: cw_gc_ajax('cw_one_step_checkout_payment');void(0);"></a>
	<span id="giftcert_applied" style="display: none;">({include file='common/currency.tpl' value=$cart.info.giftcert_discount} {$lng.lbl_l_applied})</span>
</div>
