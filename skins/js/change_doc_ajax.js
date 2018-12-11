var last_update_time = 0;

function cw_hide_applet() {
    el = document.getElementById('print_applet');
    if (el) el.innerHTML = '';
}

function cw_applet_update(data) {
    el = document.getElementById('print_applet');
    if (!el) return;

    el.innerHTML = data.applet;
}

function handler_doc_save(data) {
    el_errors = document.getElementById('ajax_errors');
    if (el_errors) el_errors.innerHTML = data.errors;
    if (data.reset_error)
        dont_ask = 0;

    if (data.pos_method)
        eval('if (typeof document.pos_device_applet.print_'+data.pos_method+' == \'function\') document.pos_device_applet.print_'+data.pos_method+'()');
    if (data.reload)
        window.location.href = data.reload;
}

function handler_update_doc(data) {
	blockElements('doc_items',false);

    if (!isset(data)) return;
    if (data.update_time < last_update_time) return;

    last_update_time = data.update_time;
   
    el = document.getElementById("doc_items");
    if (el) el.innerHTML = data.products;
   
    el_errors = document.getElementById('ajax_errors');
    if (el_errors) el_errors.innerHTML = data.errors;
 
    el_total = document.getElementById('doc_total');
    if (el_total) el_total.innerHTML = data.total;

    el_gd_value = document.getElementById('gd_value');
    if (el_gd_value) el_gd_value.value=data.gd_value
    el_gd_per_value = document.getElementById('gd_value_persent');
    if (el_gd_per_value) el_gd_per_value.value=data.gd_value_persent;
    el_gd_type= document.getElementById('gd_type');
    if (el_gd_type) el_gd_type.checked = data.gd_type;

    el_vd_value= document.getElementById('vd_value');
    if (el_vd_value) el_vd_value.value = data.vd_value;
    el_vd_per_value = document.getElementById('vd_value_persent');
    if (el_vd_per_value) el_vd_per_value.value=data.vd_value_persent;

    handler_update_payment(data);

    el = document.getElementById('contents_preview');
    if (el) el.innerHTML = data.preview;

    if (data.order_info) 
    for(i in data.order_info) {
        el = document.getElementById('aom_'+i);
        if (el) el.value = data.order_info[i];
    }

    el = document.getElementById('aom_details_shipping');
    if (el) el.innerHTML = data.shipping;

    el = document.getElementById('aom_tax_name');
    if (el) el.innerHTML = data.tax_name;
    el = document.getElementById('aom_tax_cost');
    if (el) el.innerHTML = data.tax_cost;

    $('input[name="total_details[use_shipping_cost_alt]"]').attr('checked', (data.use_shipping_cost_alt == 'Y'));

}

function handler_update_payment(data) {
    cw_applet_update(data);

    el_payment = document.getElementById('gp_payment');
    if (el_payment) el_payment.value = data.payment;
    el_payment = document.getElementById('gp_paid_by_cc');
    if (el_payment) el_payment.checked = data.paid_by_cc;
    el_change = document.getElementById('gp_change');
    if (el_change) el_change.value = data.change;
}

function cw_doc_delete_item(doc_id, index) {
	blockElements('doc_items',true);

    cw_hide_applet();

    $.ajax({ 
    "url":"index.php?target=ajax&mode=aom&action=delete_item&doc_id="+doc_id+"&index="+index,
    "success":handler_update_doc, 
    "dataType":"json", 
    "type":"post"
    });
}

function cw_doc_update_item_info(doc_id, index, form_name) {
    blockElements('doc_items',true);

    cw_hide_applet();

    other_params = cw_ajax_get_form_properties(form_name);

    $.ajax({ 
    "url":"index.php?target=ajax&mode=aom&action=update_item_info&doc_id="+doc_id+"&index="+index+other_params,
    "success":handler_update_doc, 
    "dataType":"json", 
    "type":"post"
    });
}

function cw_doc_update_discount(doc_id, index, el_name) {
    cw_hide_applet();

    el = document.getElementById(el_name);
    value = el.value;
    if (el.type == 'checkbox') value = el.checked?value:'';

    $.ajax({
    "url":"index.php?target=ajax&mode=aom&action=update_discount&doc_id="+doc_id+"&param="+el_name+"&value="+value,
    "success":handler_update_doc,
    "dataType":"json",
    "type":"post"
    });
}

function cw_doc_update_payment(doc_id, index, el_name) {
    cw_hide_applet();

    el = document.getElementById(el_name); 
    value = el.value;
    if (el.type == 'checkbox') value = el.checked?value:'';
    
    $.ajax({
    "url":"index.php?target=ajax&mode=aom&action=update_payment&doc_id="+doc_id+"&param="+el_name+"&value="+value,
    "success":handler_update_payment,
    "dataType":"json",
    "type":"post"
    });
}

function cw_doc_add_item_info(doc_id, index, form_name) {
	blockElements('doc_items',true);

    cw_hide_applet();

    other_params = cw_ajax_get_form_properties(form_name);

    document.getElementById('newproduct_id').value = '';
    document.getElementById('newproduct_id_name').value = '';
    document.getElementById('newamount').value = '';

    $.ajax({
    "url":"index.php?target=ajax&mode=aom&action=add_item&doc_id="+doc_id+"&index="+index+other_params,
    "success":handler_update_doc,
    "dataType":"json",
    "type":"post"
    });
}

function cw_doc_add_item_info_by_product_id(doc_id, product_id, amount_name, is_old) {
    cw_hide_applet();

    if (!isset(is_old)) is_old = 0;
    amount = document.getElementById(amount_name).value;
    $.ajax({
    "url":"index.php?target=ajax&mode=aom&action=add_item&doc_id="+doc_id+"&newproduct_id="+product_id+"&newamount="+amount+"&is_old="+is_old,
    "success":handler_update_doc,
    "dataType":"json",
    "type":"post"
    });
}   

function cw_doc_add_item_info_by_ean(doc_id, index, form_name, is_old, prefix) {
    cw_hide_applet();

    other_params = cw_ajax_get_form_properties(form_name);
    if (is_old == 1) other_params += "&is_old=1";

    table = document.getElementById(prefix+'_ean_table');
    while(table.rows.length > 1)
        table.deleteRow(table.rows.length-1);
    document.getElementById(prefix+'_add_button').inheritedRows = [];

    document.getElementById(prefix+'_ean_0').value = '';
    if (document.getElementById(prefix+'_amount_0')) document.getElementById(prefix+'_amount_0').value = '';
    if (document.getElementById(prefix+'_discount_0')) document.getElementById(prefix+'_discount_0').value = '';

    document.getElementById(prefix+'_add_box_3').innerHTML = '';

    $.ajax({
    "url":"index.php?target=ajax&mode=aom&action=add_item_by_ean&doc_id="+doc_id+"&index="+index+other_params,
    "success":handler_update_doc,
    "dataType":"json",
    "type":"post"
    });
}

function handler_serch_products(data) {
    el = document.getElementById("search_results");
    if (el) el.innerHTML = data.products;
}

function cw_doc_search_products(doc_id, form_name) {
    other_params = cw_ajax_get_form_properties(form_name);
   
/* 
    obj = eval("document.forms."+form_name);
    for(i in obj.elements) {
        el = obj.elements[i];

        if (!el || !el.tagName || !el.name)
            continue;

        var tName = el.tagName.toUpperCase();
        if (el.name && (tName == 'INPUT' || tName == 'SELECT' || tName == 'TEXTAREA'))
            if (el.type == 'checkbox') {
                if (el.name.search(/exact/) != -1) el.checked = true;
                else el.checked = false;
            }
            else el.value = '';
    }
*/

    $.ajax({
    "url":"index.php?target=ajax&mode=aom&action=search_products&doc_id="+doc_id+other_params,
    "success":handler_serch_products,
    "dataType":"json",
    "type":"post"
    });
}

function cw_search_by_submit(evt, doc_id) {
    evt = (evt) ? evt : event;
    var target = (evt.target) ? evt.target : evt.srcElement;
    var form = target.form;
    var charCode = (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
    if (charCode == 13) {
        cw_doc_search_products(doc_id, form.name);
        return false;
    }
    return true; 
}

function cw_doc_save_ajax(doc_id, is_invoice) {
    $.ajax({
    "url":"index.php?target=ajax&mode=aom&action=save_doc&doc_id="+doc_id+"&is_invoice="+is_invoice,
    "success":handler_doc_save,
    "dataType":"json",
    "type":"post"
    });
}


/*
 * Part of ajax/ajax_lib.js removed in rev 99
 */
function URLEncode(plaintext) {
    var SAFECHARS = "0123456789" +                  // Numeric
                    "ABCDEFGHIJKLMNOPQRSTUVWXYZ" +  // Alphabetic
                    "abcdefghijklmnopqrstuvwxyz" +
                    "-_.!~*'()";                    // RFC2396 Mark characters
    var HEX = "0123456789ABCDEF";

    var encoded = "";
    for (var i = 0; i < plaintext.length; i++ ) {
        var ch = plaintext.charAt(i);
        if (ch == " ") {
            encoded += "+";             // x-www-urlencoded, rather than %20
        } else if (SAFECHARS.indexOf(ch) != -1) {
            encoded += ch;
        } else {
            var charCode = ch.charCodeAt(0);
            if (charCode > 255) {
                alert( "Unicode Character '"
                        + ch
                        + "' cannot be encoded using standard URL encoding.\n" +
                          "(URL encoding only supports 8-bit characters.)\n" +
                          "A space (+) will be substituted." );
                encoded += "+";
            } else {
                encoded += "%";
                encoded += HEX.charAt((charCode >> 4) & 0xF);
                encoded += HEX.charAt(charCode & 0xF);
            }
        }
    } // for
    return encoded;
}

function cw_ajax_get_form_properties(form_name) {
    var getstr = "";

    obj = eval("document.forms."+form_name);
    obj_els = obj.elements;
    for(i = 0; i < obj_els.length; ++i) {
        el = obj_els[i];

        if (!el || !el.tagName || !el.name)
            continue;

        var tName = el.tagName.toUpperCase();
        if (el.name && (tName == 'INPUT' || tName == 'SELECT' || tName == 'TEXTAREA') && (!el.type || (el.type == 'checkbox' && el.checked) || el.type != 'checkbox')) {
        	if (el.multiple && tName == 'SELECT') {
        		for(j = 0; j < el.length; j++) {
        			if (el[j].selected) {
        				getstr += "&"+el.name+"="+URLEncode(el[j].value);
        			}
        		}
        	}
        	else {
        		getstr += "&"+el.name+"="+URLEncode(el.value);
        	}
        }
    }
    return getstr;
}
