function cw_get_pop_param(i) {
    arr = new Array();
    arr[0] = 'cat_id';
    arr[1] = 'supplier_id';
    return arr[i];
}

function popup_product (field_productid, field_product, params) {
    str = '';
    for(i in params)
        if (isset(params[i])) str = str + '&'+cw_get_pop_param(i)+'='+params[i];
	return window.open("index.php?target=popup_product&field_productid="+field_productid+"&field_product="+field_product+str, "selectproduct", "width=600,height=550,toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no");
}

function popup_products (field_productid, field_product, field_amount, params) {
    if (!isset(field_amount)) field_amount = '';
    str = '';
    for(i in params)
        if (isset(params[i])) str = str + '&'+cw_get_pop_param(i)+'='+params[i];
        
	if ($('#products_dialog').length==0)
		$('body').append('<div id="products_dialog"></div>');
	
		
	var hash = field_productid+field_product+field_amount+str;
	if (hash != $('#products_dialog').data('hash')) {
		// Load iframe with image selector into dialog
		$('#products_dialog').html("<iframe scrolling='auto' frameborder='no' width='830' height='480' src='index.php?target=popup_products&field_product_id="+field_productid+"&field_product="+field_product+"&field_amount="+field_amount+str+"'></iframe>");
	}
		
	$('#products_dialog').data('hash', hash);
    // Show dialog
    sm('products_dialog', 850, 530, false, 'Select product');
}

function psw_ready(search_id, search_name, search_amount) {
	$(search_name).keyup(function() {
		if ($(search_name).val().length >= 3) {
			$('html').css('cursor', 'wait');
		}
	})

	$(search_name).autocomplete({
		source: function(request, response) {
			$.ajax({
				type: 'get',
				url: 'index.php?target=products&mode=ajax_search&origin='+current_target,
				data: 'search='+encodeURIComponent(request.term),
				dataType: 'json',
				success: function(data) {
					response($.map(data, function(item) {
						return {
							id: item.id,
							label: item.label,
							value: item.value
						}
					}));
				},
				error: function() {
					if ( window.console && window.console.log ) console.log('Error occured (debug: JS ajax_search)');
				},
				complete: function() {
					$('html').css('cursor', 'auto');
				}
			})
		},
		select: function(event, ui) {
			if (ui.item.id != 0) {
				$(search_id).val(ui.item.id);
				$(search_name).val(ui.item.value);
				$(search_amount).val(1);
			}
		},
		minLength: 3
	});

	/* this allows us to pass in HTML tags to autocomplete. Without this they get escaped */
	$["ui"]["autocomplete"].prototype["_renderItem"] = function(ul, item) {
		return $("<li></li>")
			.data("item.autocomplete", item)
			.append($("<a></a>").html(item.label))
			.appendTo(ul);
	};
}

function psw_add_inputset_row(event, name, index) {
	var search_id = "#" + name + "_id_item_" + index;
	var search_name = "#" + name + "_name_item_" + index;
	var search_amount = "#" + name + "_qty_item_" + index;
	psw_ready(search_id, search_name, search_amount);
}

function psw_popup_products(obj, id_prefix, name_prefix, field_amount, params) {
	var product_id_field = id_prefix + obj.id;
	var product_name_field = name_prefix + obj.id;
	var product_amount_field = '';
	if (field_amount) {
		var product_amount_field = field_amount + obj.id;
	}

	popup_products(product_id_field, product_name_field, product_amount_field, params);
}
