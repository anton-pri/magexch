function dod_popup_products (obj, id_prefix, name_prefix) {
	var product_id_field = id_prefix + obj.id;
	var product_name_field = name_prefix + obj.id;

	popup_products(product_id_field, product_name_field);
}

function check_bonus_D_entries(element) {
/*
	return $('#dod_bonuses_D:checked').length>0 &&
		$('#dod-discount-products:checked').length>0 &&
		$('#dod_cats_catid_0:blank').length>0;		
*/
//    return $('#dod-discount-products:checked').length>0 && $('#dod_cats_catid_0:blank').length>0;
//    return true; 
   return $('#dod_cats_catid_0:blank').length>0; 
}
function check_bonus_S_entries(element) {
	return $('#dod_bonuses_S:checked').length>0 &&
		$('#dod-freeship-products:checked').length>0 &&
		$('#dod_cats3_catid_0:blank').length>0;		
}  

function dod_expand_applied_sections() {
	$('td.dod-checkbox').find('input[type="checkbox"][name^="dod_bonus"]').each(
		function(i) {
			if ($(this).is(':checked')) {
				$(this).parent('td:first').next().find('a.dod-bonus-picker').click();
			}

		}
	);
	$('td.dod-checkbox').find('input[type="checkbox"][name^="dod_cond"]').each(
		function(i) {
			if ($(this).is(':checked')) {
				$(this).parent('td:first').next().find('a.dod-bonus-picker').click();
			}

		}
	);	
}
function dod_show_cats(elm, group) {
	var cat_id = explode('_', elm.id);
	var index=cat_id.pop();
	var el = document.getElementById(group + '_catid_'+index).value; 
	return cw_show_categories(el, group, index);
}

$(document).ready(function(){

	$('a.dod-bonus-picker').click(
		function() {
			$(this).toggleClass('dod-active');
			var discount_id = $(this).parents('tr:first').attr('id') + '-details';
			var discount = $('#' + discount_id);
			if (discount != undefined) {
				discount.toggle();
			}
		}
	);
	
	$('.dod-chooser').each(
		function(i) {
			var _elm_id = $(this).attr('id');
            if (_elm_id != undefined && _elm_id.length != 0) {
            	_elm_id = '#' + _elm_id;
                $(_elm_id).click(
                	function() {
                        var id_chunks = $(this).attr('id').split('-');
                        var _id_ = 'dod-' + id_chunks[1] + '-products';

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

	$('form[name="generator_details"]:first').find('div.tabs:first').find('span').each(
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
        			$('form[name="generator_details"]').find('input[name="js_tab"]').val(_mode);
        		}
        	);
		}
	);

	if ($('#dod-freeship-products').is(':checked')) {
		$('#dod-freeship-products').click();
	}

	if ($('#dod-discount-products').is(':checked')) {
		$('#dod-discount-products').click();
	}


	dod_expand_applied_sections();

	$('#dod-refresh-list').click(function() {
		$('#dod-coupons-list').html('loading data...');		
		$('#dod-coupons-list').load(dod_url_get_coupons + "&dod_type=C", function(response, status, xhr) {
	        if (status == "error") {
	            var msg = "Sorry but there was an error: ";
	            $('#dod-coupons-list').html(msg + xhr.status + " " + xhr.statusText);
	        } else {
				if (coupon_code != undefined && coupon_code != 1) {
					$('#dod-coupons-list').find('input[type="radio"][value="' + coupon_code + '"]').attr('checked', 'checked');
				}
	        }
		});

	});
	
// FORM VALIDATOR
  $("#generator_details").validate({
	ignore:'', // Validate all fields, even hidden
    rules: {
		// Rules for bonuses
		"dod_bonus[D][discount]": 		{ required: "#dod_bonuses_D:checked"	},
		'dod_bonus[F][products][0][id]': { required: "#dod_bonuses_F:checked"	},
		"dod_bonus[C][coupon]": 			{ required: "#dod_bonuses_C:checked"	},
		'dod_bonus[D][products][0][id]': { required: check_bonus_D_entries },
		'dod_bonus[S][products][0][id]': { required: check_bonus_S_entries },
	},
	messages: {
		'dod_bonus[D][products][0][id]': { required: 'Select at least one product or category' },
		'dod_bonus[S][products][0][id]': { required: 'Select at least one product or category' },
		"dod_bonus[C][coupon]":          { required: "Please select one coupon code"	},
	},
	invalidHandler: function() {
		dod_expand_applied_sections();
	}
  });
  
});
