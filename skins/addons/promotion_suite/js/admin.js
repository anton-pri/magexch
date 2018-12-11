function ps_popup_products (obj, id_prefix, name_prefix) {
	var product_id_field = id_prefix + obj.id;
	var product_name_field = name_prefix + obj.id;

	popup_products(product_id_field, product_name_field);
}

function check_bonus_D_entries(element) {
	return $('#ps_bonuses_D:checked').length>0 &&
		$('#ps-discount-products:checked').length>0 &&
		$('#ps_cats_catid_0:blank').length>0;		
}
function check_bonus_S_entries(element) {
	return $('#ps_bonuses_S:checked').length>0 &&
		$('#ps-freeship-products:checked').length>0 &&
		$('#ps_cats3_catid_0:blank').length>0;		
}  
function check_condition_P_entries(element) {
	return $('input[name="ps_conditions[P]"]:checked').length>0 &&
		$('#ps_cats4_catid_0:blank').length>0 &&
		$('#ps_mans_id_0').val()==0 &&
		$('input[name^="ps_conds[P][attr]"]').length==0;
}  

function ps_expand_applied_sections() {
	$('td.ps-checkbox').find('input[type="checkbox"][name^="ps_bonus"],input[type="checkbox"][name^="ps_cond"]').each(
		function(i) {
			if ($(this).is(':checked')) {
                var discount_id = $(this).parents('tr:first').attr('id') + '-details';
                var discount = $('#' + discount_id);
                if (discount != undefined) {
                    discount.show();
                }
				$(this).parent('td:first').next().find('a.ps-bonus-picker').addClass('ps-active');
			}

		}
	);
}
function ps_show_cats(elm, group) {
	var cat_id = explode('_', elm.id);
	var index=cat_id.pop();
	var el = document.getElementById(group + '_catid_'+index).value; 
	return cw_show_categories(el, group, index);
}

$(document).ready(function(){

	$('a.ps-bonus-picker').click(
		function() {
			$(this).toggleClass('ps-active');
			var discount_id = $(this).parents('tr:first').attr('id') + '-details';
			var discount = $('#' + discount_id);
			if (discount != undefined) {
				discount.toggle();
			}
		}
	);
	
	$('.ps-chooser').each(
		function(i) {
			var _elm_id = $(this).attr('id');
            if (_elm_id != undefined && _elm_id.length != 0) {
            	_elm_id = '#' + _elm_id;
                $(_elm_id).click(
                	function() {
                        var id_chunks = $(this).attr('id').split('-');
                        var _id_ = 'ps-' + id_chunks[1] + '-products';

                        if ($(this).attr('id') == _id_) {
                	        $('#' + _id_ + '-block').show();
                        }
                        else {
                            $('#' + _id_ + '-block').hide();
                        }
                	}
                );
            }
		}
	);

	$('form[name="offer_details"]:first').find('div.tabs:first').find('span').each(
		function(i) {
			var tab_id = $(this).attr('id');
			if (tab_id == undefined || tab_id.length == 0) {
				return;
			}
        	$(this).click(
        		function() {
					var _mode = tab_id.replace('tab_', '');
					if (_mode.length == 0) {
						return;
					}
        			$('form[name="offer_details"]').find('input[name="js_tab"]').val(_mode);
        		}
        	);
		}
	);

	if ($('#ps-freeship-products').is(':checked')) {
		$('#ps-freeship-products').click();
	}

	if ($('#ps-discount-products').is(':checked')) {
		$('#ps-discount-products').click();
	}


	ps_expand_applied_sections();

	$('#ps-refresh-list').click(function() {
		$('#ps-coupons-list').html('loading data...');		
		$('#ps-coupons-list').load(ps_url_get_coupons + "&ps_type=C", function(response, status, xhr) {
	        if (status == "error") {
	            var msg = "Sorry but there was an error: ";
	            $('#ps-coupons-list').html(msg + xhr.status + " " + xhr.statusText);
	        } else {
				if (coupon_code != undefined && coupon_code != 1) {
					$('#ps-coupons-list').find('input[type="radio"][value="' + coupon_code + '"]').attr('checked', 'checked');
				}
	        }
		});

	});
	
// CONDITION TAB
	

	$('#ps-refresh-zones-list').click(function() {
		$('#ps-zones-list').html('loading data...');		
		$('#ps-zones-list').load(ps_url_get_zones, function(response, status, xhr) {
	        if (status == "error") {
	            var msg = "Sorry but there was an error: ";
	            $('#ps-zones-list').html(msg + xhr.status + " " + xhr.statusText);
	        } else {

	        	$.each($('#ps-zones-selector').find('option'), function() {
	        		if (ps_zones == undefined || ps_zones.length == 0) {
	        			return true;
	        		}

	        		var zone_id = $(this).val();
	        		if (zone_id.length == 0) {
	        			return;
	        		}
	        		if (ps_zones[zone_id] != undefined) {
	        			if ($(this).is(':selected')) {
	        				return;
	        			}
	        			$(this).attr('selected', 'selected');
	        		}
	        	});
		        
	        }
		});

	});

    $('#ps-cond-refresh-coupons-list').click(function() {
        $('#ps-cond-coupons-list').html('loading data...');
        $('#ps-cond-coupons-list').load(ps_url_get_coupons + "&ps_type=B", function(response, status, xhr) {
            if (status == "error") {
                var msg = "Sorry but there was an error: ";
                $('#ps-cond-coupons-list').html(msg + xhr.status + " " + xhr.statusText);
            }
            else {
                if (cond_coupon_code != undefined && cond_coupon_code != 1) {
                    $('#ps-cond-coupons-list').find('input[type="radio"][value="' + cond_coupon_code + '"]').attr('checked', 'checked');
                }
            }
        });
    });


// FORM VALIDATOR
  $("#offer_details").validate({
	ignore:'', // Validate all fields, even hidden
    rules: {
		// Rules for bonuses
		"ps_bonus[D][discount]": 		{ required: "#ps_bonuses_D:checked"	},
		'ps_bonus[F][products][0][id]': { required: "#ps_bonuses_F:checked"	},
		"ps_bonus[C][coupon]": 			{ required: "#ps_bonuses_C:checked"	},
		'ps_bonus[D][products][0][id]': { required: check_bonus_D_entries },
		'ps_bonus[S][products][0][id]': { required: check_bonus_S_entries },
		// Rules for conditions
		'ps_conds[A][zones][]': 		{ required: 'input[name="ps_conditions[A]"]:checked' },
		'ps_conds[P][products][0][id]':	{ required: check_condition_P_entries },
		"ps_conds[B][coupon]": 			{ required: 'input[name="ps_conditions[B]"]:checked' },
		
	},
	messages: {
		'ps_bonus[D][products][0][id]': { required: 'Select at least one product or category' },
		'ps_bonus[S][products][0][id]': { required: 'Select at least one product or category' },
		'ps_conds[P][products][0][id]': { required: 'Select at least one product or category or manufacturer' },
		"ps_bonus[C][coupon]": 			{ required: "Please select one coupon code"	},
		"ps_conds[B][coupon]": 			{ required: 'Please select one coupon code' },	
	},
	invalidHandler: function(event,validator) {
		ps_expand_applied_sections();
       
        /*
         * Switch to tab with first error
         */
        // Get name of first error element
        for (var prop in validator.invalid) {
            var inv_name = prop;
            break;
        }
        // Get the whole tab content container
        var cont = $('#offer_details [name="'+inv_name+'"]').parents('div[id^="contents_ps_offer"]:first');
        // Get part of its id to build name of tab
        var prefix = new String(cont.attr('id')).replace('contents_','');
        if (prefix!=undefined) {
            // Switch to tab
            switchOn('tab_'+prefix,'contents_'+prefix,prefix,'ps_offer');
        }
	}
  });
  
});
