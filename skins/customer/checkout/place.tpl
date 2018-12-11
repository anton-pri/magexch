{include_once file='js/check_cc_number_script.tpl'}

<form action="index.php?target=place_order" method="post" name="checkout_form" id="checkout_form">
<input type="hidden" name="action" value="place_order" />

{include file="customer/checkout/notes.tpl"}

<div class="place_order">
<div class="checkboxes">
    <input type="checkbox" name="terms_conditions" id="terms_conditions" value="Y" /> {$lng.lbl_osc_terms_and_conditions_note}
</div>

<script type="text/javascript">
<!--
var addons_quote_system = "{$addons.quote_system}";
var to_block = {$from_quote};
var customer_id = {$customer_id};
{literal}
	function check_quote_button() {
		if (!to_block && customer_id != 0 && addons_quote_system != "") {
			$('#request_for_quote').css("display", "block");
		}
	}
	check_quote_button();
{/literal}
-->
</script>

{assign var="button_href" value="javascript: cw_one_step_checkout_check_register('');void(0);"}
{assign var="button_href_quote" value="javascript: cw_one_step_checkout_check_register('request_for_quote');void(0);"}
<!-- cw@checkout_buttons [ -->
	<table>
		<tr><td>
	    	{include file="buttons/button.tpl" button_title=$lng.lbl_submit_order href=$button_href style='btn'}
	    </td><td>
	    	<div id="request_for_quote" style="display:none">
	    		{include file="buttons/button.tpl" button_title=$lng.lbl_request_for_quote href=$button_href_quote style='btn'}
	    	</div>
	    </td></tr>
    </table>
<!-- cw@checkout_buttons ] -->

<h2 id='msg' style="display: none; text-align: center;">{$lng.msg_order_is_being_placed}</h2>

</div>
</form>