function show_shipping_estimate_dialog(id){
	$("#shipping_estimate_dialog").html('<iframe id="shipping_estimate_modal_iframe" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" />').dialog("open");
	$("#shipping_estimate_modal_iframe").attr("src", current_location + "/index.php?target=popup-shipping&product_id=" + id);
	return false;
}

$(document).ready(function() {
	$("#shipping_estimate_dialog").dialog({
		autoOpen: false,
		modal	: true,
		height	: 460,
		width	: 740
	});
});