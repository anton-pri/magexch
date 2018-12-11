/*
	Rebuild page if some options is changed
*/
function check_options(pid) {
	var local_taxes = [];
	var is_rebuild_wholesale = false;
	var variantid = false;

	for (var t in taxes[pid])
		local_taxes[t] = taxes[pid][t][0];
	price[pid] = default_price[pid];

	/* Find variant */
	for (var x in variants[pid]) {

		if (variants[pid][x][1].length == 0)
			continue;

		variantid = x;
		for (var c in variants[pid][x][1]) {

			if (getPOValue(pid, c) != variants[pid][x][1][c]) {
				variantid = false;
				break;
			}
		}

		if (variantid)
			break;
	}

	/* If variant found ... */
	if (variantid) {
		price[pid] = variants[pid][variantid][0][0];
		orig_price[pid] = variants[pid][variantid][0][4];
//		avail = variants[variantid][0][1];
        avail[pid] = variant_avails[pid][variantid];

		/* Get variant wholesale prices */
		if (variants[pid][variantid][3]) {
			product_wholesale[pid] = [];
			for (var t in variants[pid][variantid][3]) {
				var _tmp = modi_price(pid, variants[pid][variantid][3][t][2], cloneObject(variants[pid][variantid][3][t][3]), variants[pid][variantid][3][t][4]);
				product_wholesale[pid][t] = [
					variants[pid][variantid][3][t][0],
					variants[pid][variantid][3][t][1],
					_tmp[0],
					[]
				];

				/* Get variant wholesale taxes */
				for (var c in _tmp[1]) {
					product_wholesale[pid][t][3][c] = _tmp[1][c];
				}
			}
			is_rebuild_wholesale = true;
		}

		/* Get variant taxes */
		for (var t in local_taxes) {
			if (variants[pid][variantid][2][t])
				local_taxes[t] = parseFloat(variants[pid][variantid][2][t]);
		}

		if (!product_thumbnail[pid])
			product_thumbnail[pid] = document.getElementById('product_thumbnail_' + pid);

		/* Change product thumbnail */
		if (product_thumbnail[pid]) {
			if (variants[pid][variantid][0][2].src && variants[pid][variantid][0][2].width > 0 && variants[pid][variantid][0][2].height > 0) {
				if (product_thumbnail[pid].src != variants[pid][variantid][0][2].src) {
					product_thumbnail[pid].src = variants[pid][variantid][0][2].src;
					product_thumbnail[pid].width = variants[pid][variantid][0][2].width;
					product_thumbnail[pid].height = variants[pid][variantid][0][2].height;
				}
			} else if (document.getElementById('product_thumbnail_' + pid).src != product_image[pid].src) {
				product_thumbnail[pid].src = product_image[pid].src;
				if (product_image[pid].width > 0 && product_image[pid].height > 0) {
					product_thumbnail[pid].width = product_image[pid].width;
					product_thumbnail[pid].height = product_image[pid].height;
				}
			}
		}

		/* Change product code */
		if (document.getElementById('product_code_' + pid))
			document.getElementById('product_code_' + pid).innerHTML = variants[pid][variantid][0][5];

	}

	/* Find modifiers */
	var _tmp = modi_price(pid, price[pid], local_taxes, orig_price[pid]);
	price[pid] = _tmp[0];
	local_taxes = _tmp[1];
	if (!variantid) {
		product_wholesale[pid] = [];
		for (var t in _product_wholesale[pid]) {
			_tmp = modi_price(pid, _product_wholesale[pid][t][2], _product_wholesale[pid][t][3].slice(0), _product_wholesale[pid][t][4]);
			product_wholesale[pid][t] = [
				_product_wholesale[pid][t][0],
				_product_wholesale[pid][t][1],
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
		current_taxes[pid][t] = local_taxes[t];
	}

	if (is_rebuild_wholesale)
		rebuild_wholesale(pid);

	/* Update price */
	if (document.getElementById('product_price_' + pid))
		document.getElementById('product_price_' + pid).innerHTML = currency_symbol+price_format(price[pid] < 0 ? 0 : price[pid]);

	/* Update product quantity */
	if (document.getElementById('product_avail_txt_' + pid)) {
		if (avail[pid] > 0) {
			document.getElementById('product_avail_txt_' + pid).innerHTML = lbl_in_stock + " (" + avail[pid] + ")";//substitute(txt_items_available, "items", (variantid ? avail[pid] : 1));
		} else {
			document.getElementById('product_avail_txt_' + pid).innerHTML = txt_out_of_stock;
		}
	}

	if ((mq > 0 && avail[pid] > mq+min_avail[pid]) || is_unlimit)
		avail[pid] = mq+min_avail[pid]-1;

	if ((alert_msg == 'Y') && (min_avail[pid] > avail[pid]))
		alert(txt_out_of_stock);
	
	/* Check exceptions */
	var ex_flag = check_exceptions(pid);
	if (!ex_flag && (alert_msg == 'Y'))
		alert(exception_msg);
			
	if (document.getElementById('exception_msg_' + pid))
		document.getElementById('exception_msg_' + pid).innerHTML = (ex_flag ? '' : exception_msg_html+"<br /><br />");

    if (window.localStorage) {
        var localData = {};
        for (var o in store_options[pid]) {
            localData['po' + store_options[pid][o]] = document.getElementById('po' + pid + '_' + store_options[pid][o]).value;
        }
        var data = JSON.stringify(localData);
        window.localStorage.setItem(pid, data);
    }

	return true;
}

/*
	Calculate product price with price modificators 
*/
function modi_price(pid, _price, _taxes, _orig_price) {
    var return_price = round(_price, 2);

	/* List modificators */
	for (var x2 in modifiers[pid]) {
		var value = getPOValue(pid, x2);
		if (!value || !modifiers[pid][x2][value])
			continue;

		/* Get selected option */
		var elm = modifiers[pid][x2][value];
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

/*
	Check product options exceptions
*/
function check_exceptions(pid) {
	if (!exceptions[pid])
		return true;

	/* List exceptions */
	for (var x in exceptions[pid]) {
		if (isNaN(x))
			continue;

		var found = true;
        for (var c in exceptions[pid][x]) {
			var value = getPOValue(pid, c);
			if (!value)
				return true;

            if (value != exceptions[pid][x][c]) {
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
function rebuild_wholesale(pid) {

	var obj = document.getElementById('wl_table');
	if (!obj)
		return false;

	/* Clear wholesale span object if product wholesale prices service array is empty */
	if (!product_wholesale[pid] || product_wholesale[pid].length == 0) {
		obj.innerHTML = "";
		return false;
	}

	/* Display headline */
	var str = '';
	var i = 0;
	var k = 0;
	for (var x in product_wholesale[pid]) {
		if (product_wholesale[pid][x][0] == 0)
			continue;

		if (i == 0)
			str += '<table cellpadding="2" cellspacing="0" width="100%">';

		str += '<tr';
		if (k == 0) {
			k=1;
			str += ' class="TableSubHead"';
		}
		str += '><td width="33%">'+lbl_buy+' <font class="WholesalePrice">'+product_wholesale[pid][x][0]+'</font> '+lbl_or_more+'</td>';
        str += '<td width="33%">'+lbl_pay_only+' <font class="WholesalePrice">'+price_format(product_wholesale[pid][x][2] < 0 ? 0 : product_wholesale[pid][x][2])+'</font> '+lbl_per_item+'</td>';
        wprice = price[pid] - product_wholesale[pid][x][2];
        wdiscount = (price[pid]/product_wholesale[pid][x][2])*100-100
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
function getPOValue(pid, c) {
	if (
        !document.getElementById('po'+pid+'_'+c)
        || (document.getElementById('po'+pid+'_'+c).tagName.toUpperCase() != 'SELECT'
            && document.getElementById('po'+pid+'_'+c).type != 'hidden')
    ) {
		return false;
    }

    if (document.getElementById('po'+pid+'_'+c).type == 'hidden'){
	    return document.getElementById('po'+pid+'_'+c).value;
    }
    else {
	    return document.getElementById('po'+pid+'_'+c).options[document.getElementById('po'+pid+'_'+c).selectedIndex].value;
    }
}

/*
    Get product option object by class name / class id
*/
function product_option(pid, classid) {
	if (!isNaN(classid))
		 return document.getElementById("po"+pid+'_'+classid);

	if (!names[pid])
		return false;

	for (var x in names[pid]) {
		if (names[pid][x]['class_name'] != classid)
			continue;
		return document.getElementById('po'+pid+'_'+x);
    }

	return false;
}

/*
	Get product option value by class name / or class id
*/
function product_option_value(pid, classid) {
	var obj = product_option(pid, classid);
	if (!obj)
		return false;

	if (obj.type != 'select-one')
		return obj.value;

	var classid = parseInt(obj.id.substr(2));
	var optionid = parseInt(obj.options[obj.selectedIndex].value);
	if (names[pid][classid] && names[pid][classid]['options'][optionid])
		return names[pid][classid]['options'][optionid];

	return false;
}

