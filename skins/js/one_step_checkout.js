var handlerSections = new Array();
handlerSections['cw_one_step_checkout_login'] = ['onestep_login','onestep_method','onestep_place'];
handlerSections['cw_one_step_checkout_payment'] = ['onestep_method','onestep_place'];
var update_fieldsaddresscallback = false;

function toggleSections(handler,enable) {

	if (handlerSections[handler] && handlerSections[handler].length > 0) {

        for (var sec in handlerSections[handler]) {        	
            var a = '#'+handlerSections[handler][sec];

            if (enable) {
            	$(a).unblock();
            }
            else {
            	$(a).block({
            		message: null,
            		theme: true
            	});
            }
        }
	}
}

function cw_check_address() {
    var is_same = $('#is_same').is(':checked');
    $('#current_address').css('display', is_same?'none':'');
    if ($('#address_modify').length==0) $('#apply_address').hide();
    toggleSections('cw_one_step_checkout_payment',false);
    $.ajax({
        "url":app_web_dir+"/index.php?target=cart&is_ajax=1&action=address_update&mode=checkout",
        "success": cw_one_step_checkout_payment,
        "dataType":"xml",
        "type": "post"
    });
}

function cw_submit_form_ajax(form_name, handler) {

    toggleSections(handler, false);

    var form = $('form[name='+form_name+']');
    $.ajax({
        'type': 'post',
        'url': form.attr('action')+'&is_ajax=1',
        'data': form.serialize(),
        'success': eval(handler),
        'error': function() {alert('Error occured (debug: JS cw_submit_form_ajax). Form name='+form_name+' Action='+form.attr('action'));},
        'dataType': 'xml'
    });

}

function cw_submit_post_ajax(url, data, handler) {
	
	toggleSections(handler, false);

	$.ajax({
		'type': 'post',
		'url': url + '&is_ajax=1',
		'data': data,
		'success': eval(handler),
		'error': function() {alert('Error occured (debug: JS cw_submit_post_ajax)');},
		'dataType': 'xml'
	});	
}

function cw_submit_get_ajax(url, handler) {

    toggleSections(handler, false);

    $.ajax({
        'type': 'get',
        'url': url+'&is_ajax=1',
        'success': eval(handler),
        'error': function() {alert('error');},
        'dataType': 'xml'
    });
}

function cw_one_step_checkout_login(xml) {

    toggleSections('cw_one_step_checkout_login',true);

    if ($(xml).find('error').text()) {
        sm('box', 500, 200);
        $('#txt').html($(xml).find('error').text());
    }
    else {
        hm('box');
        var auth_top = $('#top_auth');
        if (auth_top) auth_top.html($(xml).find('auth_menu').text());
        var cart_menu_el = $('#content_menu_cart');
        if (cart_menu_el) cart_menu_el.html($(xml).find('cart_menu').text());

        var onestep_login = $('#onestep_login');
        onestep_login.html($(xml).find('onestep_login').text());

        var head_menu = $('#log.login');
        if (head_menu) head_menu.html($(xml).find('head_login').text());
        var head_menu = $('#register');
        if (head_menu) head_menu.html($(xml).find('head_register').text());

        var onestep_method = $('#onestep_method');
        onestep_method.html($(xml).find('onestep_method').text());

        var onestep_payment = $('#onestep_payment');
        onestep_payment.html($(xml).find('onestep_payment').text());

        /* Hide second radio option with label */
        $('#onestep_option input:radio:last').parent().hide();

	//ajaxGet('index.php?target=custom_saratogawine_shipping_incentives&mode=header&redirect_to='+encodeURIComponent(window.location.href));
/*
        var b_country = document.getElementById('b_country');
        var b_state = document.getElementById('b_state_value');
        cw_map_ajax_update_states_list(b_country.value, '-1', 'b_state', b_state.value, '', '', '');

        var s_country = document.getElementById('s_country');
        var s_state = document.getElementById('s_state_value');
        cw_map_ajax_update_states_list(s_country.value, '-1', 's_state', s_state.value, '', '', '');
*/
		if ($(xml).find('message').text()) {
			sm('box');
			$('#txt').html($(xml).find('message').text());
		}
    }
}

function cw_one_step_checkout_register(xml) {
    hm('box');

    if ($(xml).find('error').text()) {
        sm('box', 500, 200);
        $('#txt').html($(xml).find('error').text());
    }
    else {
// kornev, we cannot apply that - it will cleanup the payment form, but it doesn't matter
// in any case the page will be reloaded with the payment submit
//        cw_one_step_checkout_login(xml);
        $('btn_box').css('display', 'none');
        $('msg').css('display', 'none');
        document.checkout_form.submit();
    }
}

function cw_one_step_checkout_payment(xml) {
    toggleSections('cw_one_step_checkout_payment',true);
    hm('box');

    var onestep_method = $('#onestep_method');
    onestep_method.html($(xml).find('onestep_method').text());

    var onestep_payment = $('#onestep_payment');
    onestep_payment.html($(xml).find('onestep_payment').text());

        if ($(xml).find('message').text()) {
            $('#txt').html($(xml).find('message').text());
            sm('box', 500, 200);
        }

    if (customer_id > 0 && typeof check_quote_button == "function") check_quote_button();
}

function cw_one_step_checkout_cart(xml) {
    sm('box',500,200);
    $('#txt').html($(xml).find('cart').text());
}

function cw_one_step_checkout_dialog(text) {
    sm('box',500,200);
    document.getElementById('txt').innerHTML = document.getElementById(text).innerHTML;
}

function cw_checkout_save_addresses() {


        if (customer_id>0) {
            if ($('#address').validate().form()) {
            	    $('#apply_address').hide();
                submitFormPart('address',cw_checkout_init);            
            }
        } else {
            if ($('#profile_form').validate().form())
                cw_submit_form_ajax('register_form',cw_one_step_checkout_login);
        }
}

function cw_checkout_same_address() {
    var is_same = $('#is_same').is(':checked')?1:0;
    ajaxGet(app_web_dir+"/index.php?target=user&mode=addresses&is_ajax=1&is_checkout=1&action=set_same&same="+is_same,null, cw_check_address);
}

function cw_checkout_init() {
    cw_register_init();
    $('div#address_book_wrapper').dialog({autoOpen: false, minWidth: 730, height: 220, modal: true});
    $('a.control').bind('click',function() {$('#apply_address').show();});

    cw_submit_get_ajax(app_web_dir+"/index.php?target=cart&is_ajax=1&action=update&mode=checkout", 'cw_one_step_checkout_payment');
    if (checkout_step == 1) $('a.control').click();
}

jQuery.validator.addMethod("validate_existing_email_remote", cw_validate_existing_email_remote, "");

function cw_validate_existing_email_remote(value, element) {
    var isGuestChecked = $('#guest_option').attr('checked')?1:0;
    if (!isGuestChecked)
        ajaxGet('index.php?mode=check_email&email='+value+'&guest='+isGuestChecked);
    return true;
}
