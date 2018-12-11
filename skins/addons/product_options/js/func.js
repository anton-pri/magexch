var current_taxes = [];
var product_thumbnail = document.getElementById('product_thumbnail');
//var availObj = document.getElementById('product_avail');

/*
	Rebuild page if some options is changed
*/
function check_options() {
	var local_taxes = [];
	var is_rebuild_wholesale = false;

	for (var t in taxes)
		local_taxes[t] = taxes[t][0];
	price = default_price;

    var selected_options = currect_selected_product_options();

    var variantid = current_selected_variant(selected_options);

	/* If variant found ... */
	if (variantid) {
		price = variants[variantid][0][0];
		orig_price = variants[variantid][0][4];
//		avail = variants[variantid][0][1];
        avail = variant_avails[variantid];

		/* Get variant wholesale prices */
		if (variants[variantid][3]) {
			product_wholesale = [];
			for (var t in variants[variantid][3]) {
				var _tmp = modi_price(variants[variantid][3][t][2], cloneObject(variants[variantid][3][t][3]), variants[variantid][3][t][4]);
				product_wholesale[t] = [
					variants[variantid][3][t][0], 
					variants[variantid][3][t][1], 
					_tmp[0],
					[]
				];

				/* Get variant wholesale taxes */
				for (var c in _tmp[1]) {
					product_wholesale[t][3][c] = _tmp[1][c];
				}
			}
            list_price = variants[variantid][3][0][5];
			is_rebuild_wholesale = true;
		}

		/* Get variant taxes */
		for (var t in local_taxes) {
			if (variants[variantid][2][t])
				local_taxes[t] = parseFloat(variants[variantid][2][t]);
		}

		if (!product_thumbnail)
			product_thumbnail = document.getElementById('product_thumbnail');

		/* Change product thumbnail */
		if (product_thumbnail) {
			if (variants[variantid][0][2].src && variants[variantid][0][2].width > 0 && variants[variantid][0][2].height > 0) {
				if (product_thumbnail.src != variants[variantid][0][2].src) {
					product_thumbnail.src = variants[variantid][0][2].src;
//					product_thumbnail.width = variants[variantid][0][2].width;
//					product_thumbnail.height = variants[variantid][0][2].height;
				}
			} else if (document.getElementById('product_thumbnail').src != product_image.src) {
				product_thumbnail.src = product_image.src;
				if (product_image.width > 0 && product_image.height > 0) {
//					product_thumbnail.width = product_image.width;
//					product_thumbnail.height = product_image.height;
				}
			}
			
			// Integration with detailed_product_images addon
			if (is_zoomifier) {
				if ($("#dpi-thumbnails a[variant_id='"+variantid+"']").length>0) {
					$("#dpi-thumbnails a[variant_id='"+variantid+"']").click();
				} else {
					$("#dpi-thumbnails a:first").click();
				}
			}
		}

		/* Change product weight */
		if (document.getElementById('product_weight'))
			document.getElementById('product_weight').innerHTML = price_format(variants[variantid][0][3]);
		if (document.getElementById('product_weight_box'))
			document.getElementById('product_weight_box').style.display = parseFloat(variants[variantid][0][3]) > 0 ? "" : "none";

		/* Change product code */
		if (document.getElementById('product_code'))
			document.getElementById('product_code').innerHTML = variants[variantid][0][5];

	}

	/* Find modifiers */
	var _tmp = modi_price(price, local_taxes, orig_price);
	price = _tmp[0];
	local_taxes = _tmp[1];
	if (!variantid) {
		product_wholesale = [];
		for (var t in _product_wholesale) {
			_tmp = modi_price(_product_wholesale[t][2], _product_wholesale[t][3].slice(0), _product_wholesale[t][4]);
			product_wholesale[t] = [
				_product_wholesale[t][0],
				_product_wholesale[t][1],
				_tmp[0],
				_tmp[1]
			];
		}
		is_rebuild_wholesale = true;
	}

	/* Update taxes */
	for (var t in local_taxes) {
		if (document.getElementById('tax_'+t))
			$('#tax_'+t).html(currency_symbol+price_format(local_taxes[t] < 0 ? 0 : local_taxes[t]));
		current_taxes[t] = local_taxes[t];
	}

	if (is_rebuild_wholesale)
		rebuild_wholesale();

	/* Update price */
	if (document.getElementById('product_price'))
		document.getElementById('product_price').innerHTML = currency_symbol+price_format(price < 0 ? 0 : price);

	/* Update alt. price */
	if (alter_currency_rate > 0 && alter_currency_symbol != "" && document.getElementById('product_alt_price')) {
		var altPrice = price*alter_currency_rate;
		document.getElementById('product_alt_price').innerHTML = "("+alter_currency_symbol+" "+price_format(altPrice < 0 ? 0 : altPrice)+")";
	}

	/* Update Save % */
	if (document.getElementById('save_percent_box') && list_price > 0 && dynamic_save_money_enabled) {
        var you_save_value = list_price - price;
		var save_percent = Math.round(100-(price/list_price)*100);
		document.getElementById('save_percent_box').style.display = 'none';
        $('.list_price').hide();
        document.getElementById('save_percent_box').innerHTML = currency_symbol+price_format(you_save_value)+' ('+save_percent+'%)';
        $('.list_price').html(currency_symbol+price_format(list_price));

		if (you_save_value > 0) {
			document.getElementById('save_percent_box').style.display = '';
            $('.list_price').show();
        }
        
	}

	/* Update product quantity */
	if (document.getElementById('product_avail_txt')) {
		if (avail > 0) {
			document.getElementById('product_avail_txt').innerHTML = substitute(txt_items_available, "items", (variantid ? avail : product_avail));
		} else {
			document.getElementById('product_avail_txt').innerHTML = lbl_no_items_available;
		}
	}

	if ((mq > 0 && avail > mq+min_avail) || is_unlimit)
		avail = mq+min_avail-1;

	var select_avail = min_avail;
	/* Update product quantity selector */
//	if (!availObj)
    availObj = document.getElementById('product_avail');
	if (availObj && availObj.tagName.toUpperCase() == 'SELECT') {
		if (!isNaN(min_avail) && !isNaN(avail)) {
			var first_value = -1;
			if (availObj.options[0])
				first_value = availObj.options[0].value;

// kornev
			if (first_value == min_avail && false) {

				/* New and old first value in quantities list is equal */
				if ((avail-min_avail+1) != availObj.options.length) {
					if (availObj.options.length > avail) {
						var cnt = availObj.options.length;
						for (var x = (avail < 0 ? 0 : avail); x < cnt; x++)
							availObj.options[availObj.options.length-1] = null;
					} else {
						var cnt = availObj.options.length;
						for (var x = cnt+1; x <= avail; x++)
							availObj.options[cnt++] = new Option(x, x);
					}
				}
			} else {

				/* New and old first value in quantities list is differ */
				while (availObj.options.length > 0)
					availObj.options[0] = null;
				var cnt = 0;
				for (var x = min_avail; x <= avail; x++)
					availObj.options[cnt++] = new Option(x, x);
			}
			if (availObj.options.length == 0)
				availObj.options[0] = new Option(txt_out_of_stock, 0);
		}
		select_avail = availObj.options[availObj.selectedIndex].value;
	}

	if ((alert_msg == 'Y') && (min_avail > avail))
		alert(txt_out_of_stock);

    if (exceptions.length == 0) {
        rebuild_options_values_titles();	
    }

	/* Check exceptions */

	var ex_flag = check_exceptions();
	if (!ex_flag && (alert_msg == 'Y'))
		alert(exception_msg);
			
	if (document.getElementById('exception_msg'))
		document.getElementById('exception_msg').innerHTML = (ex_flag ? '' : exception_msg_html+"<br /><br />");

	return true;
}

/*
	Calculate product price with price modificators 
*/
function modi_price(_price, _taxes, _orig_price) {
    var return_price = round(_price, 2);

	/* List modificators */
	for (var x2 in modifiers) {
		var value = getPOValue(x2);
		if (!value || !modifiers[x2][value])
			continue;

		/* Get selected option */
		var elm = modifiers[x2][value];
		return_price += parseFloat(elm[1] == '$' ? elm[0] : (_price*elm[0]/100));

		/* Get tax extra charge */
		for (var t2 in _taxes) {
			if (elm[2][t2]) {
				_taxes[t2] += parseFloat(elm[1] == '$' ? elm[2][t2] : (_orig_price*elm[2][t2]/100));
			}
		}
	}

	return [return_price, _taxes];
}

function currect_selected_product_options() {
    var sel_options = [];
    for (var cn in names) {
        if (names[cn].options.length > 0) { 
            sel_options[cn] = getPOValue(cn);
        }
    }
    return sel_options;
}

function current_selected_variant(sel_options) { 
    var variantid = false; 
    /* Find variant */
    for (var x in variants) {
        if (variants[x][1].length == 0)
            continue;

        variantid = x;
        for (var c in variants[x][1]) {
            if (sel_options[c] != variants[x][1][c]) {
                variantid = false;
                break;
            }
        }

        if (variantid)
            break;
    }
    return variantid;
}


function rebuild_options_values_titles() {

    var sel_options = currect_selected_product_options();

    for (var cn in names) {
        if (names[cn].show_prices != null) {
            if (names[cn].show_prices == 1) {

                var selbox_id = 'po'+cn;
                if (document.getElementById(selbox_id) == null) continue;

                var sel_box = document.getElementById(selbox_id);

                document.getElementById(selbox_id).options.length = 0;

                for (var orderby_idx in names[cn].options_orderbys) {
                    var opt_id = names[cn].options_orderbys[orderby_idx];
                    var selbox_opt = document.createElement('option');
                    selbox_opt.value = opt_id;
                    selbox_opt.innerHTML = product_option_value_title(cn, opt_id, sel_options);
                    sel_box.appendChild(selbox_opt);
                }
                document.getElementById(selbox_id).value = sel_options[cn];
            }
        }
    }
}

function product_option_value_title(option_id, option_value_id, selected_options) {

    var result = names[option_id].options[option_value_id];

    if (names[option_id].show_prices != null) {
        if (names[option_id].show_prices == 1) {
            var _sel_options = cloneObject(selected_options);
            _sel_options[option_id] = option_value_id;
            var _variantid = current_selected_variant(_sel_options);
            var price = variants[_variantid][0][0];
            result += " - " + currency_symbol+price_format(price);  
        }   
    } 

    return result;
}

/*
	Check product options exceptions
*/
function check_exceptions() {
	if (exceptions.length == 0)
		return true;

     
    /* build list of allowed options for current selection */

    var allowed_options = [];
    for (var cn in names) {
        if (!names[cn].options.length) continue;

        allowed_options[cn] = [];

        var _sel_options = [];
        for (var n in names) {
            if (names[n].options.length > 0) { 
                _sel_options[n] = getPOValue(n);
            }
        }

        var i = 0;

        for (var orderby_idx in names[cn].options_orderbys) {
            var oid = names[cn].options_orderbys[orderby_idx];
            _sel_options[cn] = oid;
            if (check_exceptions_array(_sel_options)) { 
                allowed_options[cn][i++] = oid;
            } 
        }
    }

    var sel_options = [];
    for (var cn in names) {
        if (names[cn].options.length > 0) { 
            sel_options[cn] = getPOValue(cn);
        }
    }

    /* rebuild select box with allowed options */
    for (var cn in allowed_options) {
        var selbox_id = 'po'+cn; 
        if (document.getElementById(selbox_id) == null) continue;

        var sel_box = document.getElementById(selbox_id);

        document.getElementById(selbox_id).options.length = 0;

        for (var oidx in allowed_options[cn]) { 
            var selbox_opt = document.createElement('option');
            var opt_id = allowed_options[cn][oidx];
            selbox_opt.value = opt_id;
            selbox_opt.innerHTML = product_option_value_title(cn, opt_id, sel_options);
            sel_box.appendChild(selbox_opt);
        } 
        document.getElementById(selbox_id).value = sel_options[cn]; 
    }

    return check_exceptions_array (sel_options);
}

function check_exceptions_array (selected_options) { 

	/* List exceptions */
	for (var x in exceptions) {
		if (isNaN(x))
			continue;

		var found = true;
        for (var c in exceptions[x]) {
			var value = selected_options[c];
			if (!value)
				return true;

            if (value != exceptions[x][c]) {
				found = false;
				break;
			}
		}
		if (found)
			return false;
	}

	return true;
}

/*
	Rebuild wholesale tables
*/
function rebuild_wholesale() {

	var obj = document.getElementById('wl_table');
	if (!obj)
		return false;

	/* Clear wholesale span object if product wholesale prices service array is empty */
	if (!product_wholesale || product_wholesale.length == 0) {
		obj.innerHTML = "";
		return false;
	}

	/* Display headline */
	var str = '';
	var i = 0;
	var k = 0;
	for (var x in product_wholesale) {
		if (product_wholesale[x][0] == 0)
			continue;

		if (i == 0)
			str += '<table cellpadding="2" cellspacing="0" width="100%">';

		str += '<tr';
		if (k == 0) {
			k=1;
			str += ' class="TableSubHead"';
		}
		str += '><td width="33%">'+lbl_buy+' <font class="WholesalePrice">'+product_wholesale[x][0]+'</font> '+lbl_or_more+'</td>';
        str += '<td width="33%">'+lbl_pay_only+' <font class="WholesalePrice">'+price_format(product_wholesale[x][2] < 0 ? 0 : product_wholesale[x][2])+'</font> '+lbl_per_item+'</td>';
        wprice = price - product_wholesale[x][2];
        wdiscount = (price/product_wholesale[x][2])*100-100 
        str += '<td width="33%">'+lbl_you_save+' <font class="WholesalePrice">'+price_format(wprice)+' '+lbl_or+' '+price_format(wdiscount)+'%</font></td>';
//		if (x == product_wholesale.length-1) {
//			str += '+';
//		} else if (product_wholesale[x][0] < product_wholesale[x][1]) {
//			str += '-'+product_wholesale[x][1];
//		}
		str += '</tr>';
		i++;
	}

	if (i == 0)
		return false;

    /* Display wholesale prices taxes */
//	var tax_str = '';
//    if (taxes.length > 0) {
//        for (var x in taxes) {
//            if (current_taxes[x] > 0)
//                tax_str += substitute(lbl_including_tax, 'tax', taxes[x][1])+'<br />';
//        }
//    }

	/* Display wholesale prices */
//	str += '</tr><tr bgcolor="#EEEEEE"><td align="right"><b>'+lbl_price+(tax_str.length > 0 ? '*' : '')+':&nbsp;</b></td>';
//	for (var x in product_wholesale) {
//		if (product_wholesale[x][0] == 0)
//			continue;
//		str += '<td>'+price_format(product_wholesale[x][2] < 0 ? 0 : product_wholesale[x][2])+'</td>';
//	}

	str += '</table>';

//	if (tax_str.length > 0)
//		str += '<br /><table><tr><td class="FormButton" valign="top"><b>*'+txt_note+':</b>&nbsp;</td><td nowrap="nowrap" valign="top">'+tax_str+'</td></tr></table>';

	obj.innerHTML = str;

	return true;
}

/*
	Get product option value
*/
function getPOValue(c) {
	if (!document.getElementById('po'+c) || document.getElementById('po'+c).tagName.toUpperCase() != 'SELECT')
		return false;
	return document.getElementById('po'+c).options[document.getElementById('po'+c).selectedIndex].value;
}

/*
    Get product option object by class name / class id
*/
function product_option(classid) {
	if (!isNaN(classid))
		 return document.getElementById("po"+classid);

	if (!names)
		return false;

	for (var x in names) {
		if (names[x]['class_name'] != classid)
			continue;
		return document.getElementById('po'+x);
    }

	return false;
}

/*
	Get product option value by class name / or class id
*/
function product_option_value(classid) {
	var obj = product_option(classid);
	if (!obj)
		return false;

	if (obj.type != 'select-one')
		return obj.value;

	var classid = parseInt(obj.id.substr(2));
	var optionid = parseInt(obj.options[obj.selectedIndex].value);
	if (names[classid] && names[classid]['options'][optionid])
		return names[classid]['options'][optionid];

	return false;
}

