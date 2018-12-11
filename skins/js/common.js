//
// Enviroment identificator
//
var localIsDOM = document.getElementById?true:false;
var localIsJava = navigator.javaEnabled();
var localIsStrict = document.compatMode=='CSS1Compat';
var localPlatform = navigator.platform;
var localVersion = "0";
var localBrowser = "";
var localBFamily = "";
if (window.opera && localIsDOM) {
	localBFamily = localBrowser = "Opera";
	if (navigator.userAgent.search(/^.*Opera.([\d.]+).*$/) != -1)
		localVersion = navigator.userAgent.replace(/^.*Opera.([\d.]+).*$/, "$1");
	else if (window.print)
		localVersion = "6";
	else
		localVersion = "5";
} else if (document.all && document.all.item)
	localBFamily = localBrowser = 'MSIE';
if (navigator.appName=="Netscape") {
	localBFamily = "NC";
	if (!localIsDOM) {
		localBrowser = 'Netscape';
		localVersion = navigator.userAgent.replace(/^.*Mozilla.([\d.]+).*$/, "$1");
		if(localVersion != '')
			localVersion = "4";
	} else if(navigator.userAgent.indexOf("Safari") >= 0)
		localBrowser = 'Safari';
	else if (navigator.userAgent.indexOf("Netscape") >= 0)
		localBrowser = 'Netscape';
	else if (navigator.userAgent.indexOf("Firefox") >= 0)
		localBrowser = 'Firefox';
	else
		localBrowser = 'Mozilla';

}
if (navigator.userAgent.indexOf("MSMSGS") >= 0)
	localBrowser = "WMessenger";
else if (navigator.userAgent.indexOf("e2dk") >= 0)
	localBrowser = "Edonkey";
else if (navigator.userAgent.indexOf("Gnutella") + navigator.userAgent.indexOf("Gnucleus") >= 0)
	localBrowser = "Gnutella";
else if (navigator.userAgent.indexOf("KazaaClient") >= 0)
	localBrowser = "Kazaa";

if (localVersion == '0' && localBrowser != '') {
	var rg = new RegExp("^.*"+localBrowser+".([\\d.]+).*$");
	localVersion = navigator.userAgent.replace(rg, "$1");
}
var localIsCookie = ((localBrowser == 'Netscape' && localVersion == '4')?(document.cookie != ''):navigator.cookieEnabled);

function change_antibot_image(id) {
	var image = document.getElementById(id);
	image.src = "temp";
	setTimeout('', 1000);
	image.src = app_web_dir+"/index.php?target=antibot_image&"+Math.random()+"&section="+id+"&regenerate=Y";
}


/*
	Find element by classname
*/
// TODO: no need this function if jQuery is used
function getElementsByClassName( clsName ) {
	var arr = new Array();
	var elems = document.getElementsByTagName("*");

	for ( var cls, i = 0; ( elem = elems[i] ); i++ ) {
		if ( elem.className == clsName ) {
			arr[arr.length] = elem;
		}
	}
	return arr;
}

/*
	URL encode
*/
function urlEncode(url) {
	return url.replace(/\s/g, "+").replace(/&/, "&amp;").replace(/"/, "&quot;")
}

/*
	Math.round() wrapper
*/
function round(n, p) {
	if (isNaN(n))
		n = parseFloat(n);
	if (!p || isNaN(p))
		return Math.round(n);
	p = Math.pow(10, p);
	return Math.round(n*p)/p;
}

/*
	Price format
*/
function price_format(price, thousand_delim, decimal_delim, precision) {
	var thousand_delim = (arguments.length > 1 && thousand_delim !== false) ? thousand_delim : number_format_th;
	var decimal_delim = (arguments.length > 2 && decimal_delim !== false) ? decimal_delim : number_format_dec;
	var precision = (arguments.length > 3 && precision !== false) ? precision : number_format_point;

	if (precision > 0) {
		precision = Math.pow(10, precision);
		price = Math.round(price*precision)/precision;
		var top = Math.floor(price);
		var bottom = Math.round((price-top)*precision)+precision;

	} else {
		var top = Math.round(price);
		var bottom = 0;
	}

	top = top+"";
	bottom = bottom+"";
	var cnt = 0;
	for (var x = top.length; x >= 0; x--) {
		if (cnt % 3 == 0 && cnt > 0 && x > 0)
			top = top.substr(0, x)+thousand_delim+top.substr(x, top.length);

		cnt++;
	}

	return (bottom > 0) ? (top+decimal_delim+bottom.substr(1, bottom.length)) : top;
}

/*
	Substitute
*/
function substitute(lbl) {
var x, rg;
	for(x = 1; x < arguments.length; x+=2) {
		if(arguments[x] && arguments[x+1]) {
			rg = new RegExp("\\{\\{"+arguments[x]+"\\}\\}", "gi");
			lbl = lbl.replace(rg,  arguments[x+1]);
			rg = new RegExp('~~'+arguments[x]+'~~', "gi");
			lbl = lbl.replace(rg,  arguments[x+1]);
		}
	}
	return lbl;
}

function getWindowWidth(w) {
	if (!w)
		w = window;
    if (localBFamily == "MSIE")
		return w.document.body.clientWidth;
    else
		return w.innerWidth;
}

function getWindowHeight(w) {
	if (!w)
		w = window;
    if (localBFamily == "MSIE")
		return w.document.body.clientHeight;
	else
		return w.innerHeight;
}

function getDocumentHeight(w){
	if (!w)
		 w = window;
	if (localBFamily == "MSIE" || (localBFamily == "Opera" && localVersion >= 7 && localVersion < 8))
		return isStrict ? w.document.documentElement.scrollHeight : w.document.body.scrollHeight;
	if (localBFamily == "NC")
		return w.document.height
	if (localBFamily == "Opera")
		return w.document.body.style.pixelHeight
}

/*
	Check list of checkboxes
*/
function checkMarks(form, reg, lbl) {
var is_exist = false;

	if (form.elements.length == 0)
		return true;

	for (var x = 0; x < form.elements.length; x++) {
		if (form.elements[x].name.search(reg) == 0 && form.elements[x].type == 'checkbox' && !form.elements[x].disabled) {
			is_exist = true;
			if (form.elements[x].checked)
				return true;
		}
	}

	if (!is_exist)
		return true;
	else if (lbl)
		alert(lbl);
	else if (lbl_no_items_have_been_selected)
		alert(lbl_no_items_have_been_selected);
	return false;
}

/*
	Submit form with specified value of 'mode' parmaeters
*/
function cw_submit_form(obj, action) {
    if (!obj.tagName) obj = eval('document.forms.'+obj);
    if (isset(action))
        obj.action.value = action;
    $(obj).submit(); // Call direct onsubmit handler and also attached handlers such as validation()
}

function submitEnter(evt) {
    evt = (evt) ? evt : event;
    var target = (evt.target) ? evt.target : evt.srcElement;
    var form = target.form;
    var charCode = (evt.charCode) ? evt.charCode :
        ((evt.which) ? evt.which : evt.keyCode);
    if (charCode == 13) {
        form.submit();
        return false;
    }
    return true;
}

/*
	Analogue of PHP function sleep()
*/
function sleep(msec) {
	var then = new Date().getTime()+msec;
	while (then >= new Date().getTime()){
	}
}

/*
	Convert number from current format
	(according to 'Input and display format for floating comma numbers' option)
	to float number
*/
function convert_number(num) {
	var regDec = new RegExp(reg_quote(number_format_dec), "gi");
	var regTh = new RegExp(reg_quote(number_format_th), "gi");
	var pow = Math.pow(10, parseInt(number_format_point));
	num = parseFloat(num.replace(" ", "").replace(regTh, "").replace(regDec, "."));
	return Math.round(num*pow)/pow;
}

/*
	Check string as number
	(according to 'Input and display format for floating comma numbers' option)
*/
function check_is_number(num) {
	var regDec = new RegExp(reg_quote(number_format_dec), "gi");
	var regTh = new RegExp(reg_quote(number_format_th), "gi");
	num = num.replace(" ", "").replace(regTh, "").replace(regDec, ".");
	return (num.search(/^[0-9]+(\.[0-9]+)?$/) != -1);
}

/*
	Qutation for RegExp class
*/
function reg_quote(s) {
	return s.replace(/\./g, "\\.").replace(/\//g, "\\/").replace(/\*/g, "\\*").replace(/\+/g, "\\+").replace(/\[/g, "\\[").replace(/\]/g, "\\]");
}

function setCookie(name, value) {
	var date = new Date();
	date.setFullYear(date.getYear()+1);
	document.cookie = name+"="+escape(value)+"; expires="+date.toGMTString();
}

function deleteCookie(name) {
	document.cookie = name+"=0; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
}

/*
	Clone object
*/
function cloneObject(orig) {
	var r = {};
	for (var i in orig) {
		r[i] = orig[i];
	}

	return r;
}

/*
	Get first checkbox and redirect to URL
*/
function getFirstCB(form, reg) {

	while (form.tagName && form.tagName.toUpperCase() != 'FORM')
		form = form.parentNode;

	if (!form.tagName || form.tagName.toUpperCase() != 'FORM' || form.elements.length == 0)
        return false;

	var selectedChk = false;
    for (var x = 0; x < form.elements.length; x++) {
        if (form.elements[x].name.search(reg) == 0 && form.elements[x].type == 'checkbox' && !form.elements[x].disabled && form.elements[x].checked) {
			selectedChk = form.elements[x];
			break;
        }
    }

    if (!selectedChk) {
		if (lbl_no_items_have_been_selected)
			alert(lbl_no_items_have_been_selected);

        return false;
	}

    return selectedChk;
}

function $oppener(id){return window.opener.document.getElementById(id);}

function isset(obj) {
    return typeof(obj) != 'undefined' && obj !== null;
}

function isFunction(f) {
    return (typeof(f) == 'function' || (typeof(f) == 'object' && (f+"").search(/\s*function /) === 0));
}

function explode(sep, str) {
    var dim = new Array();
    if (!str)
        return dim;

    var len = str.length;
    var len_sep = sep.length;
    var key = 0;
    var element = '';
    while (len > key) {
        if (str.substr(key, len_sep) == sep) {
            dim[dim.length] = element;
            element = '';
            key += len_sep - 1;
        } else {
            element += str.charAt(key);
        }
        key ++;
    }
    dim[dim.length] = element;
    return dim;
}

function sm(id, width, height, is_clear, title) {
      
    var dc = '';
    if (is_clear) dc = 'loading';

    if (!width && $('#'+id).attr('width')!==undefined) width = $('#'+id).attr('width');
    if (!height && $('#'+id).attr('height')!==undefined) height = $('#'+id).attr('height');   

	if (!width) width='auto';
	if (!height) height='auto';

    // Will be true if bootstrap is loaded, false otherwise
    var bootstrap_enabled = (typeof $().modal == 'function');
    if (bootstrap_enabled) {
        sm_bootstrap(id, width, height, is_clear, title);
    } else {
        $('#'+id).dialog({
            modal: true,
            width: width,
            height: height,
            resizable: false,
            dialogClass: dc,
            title: title
        });
    }
}

function sm_bootstrap(id, width, height, is_clear, title) {
    if ($('#bootstrap_dialog').length==0) {
        $('body').append(
        '<div id="bootstrap_dialog" class="modal fade" role="dialog" style="width:auto;height:auto;">'+
        '  <div class="modal-dialog" style="width:auto;height:auto;">'+
        '    <div class="modal-content">'+
        '      <div class="modal-header">'+
        '        <button type="button" class="close" data-dismiss="modal">&times;</button>'+
        '        <h4 class="modal-title"><!-- header --></h4>'+
        '      </div>'+
        '      <div class="modal-body">'+
        '        <!-- content -->'+
        '      </div>'+
        '    </div>'+
        '  </div>'+
        '</div>'
        );
    }
    
    if (width) $('#bootstrap_dialog .modal-dialog').width(width+12);
    if (height) $('#bootstrap_dialog .modal-dialog').height(height);
    $('#bootstrap_dialog .modal-title').text(title);
    var div_already_inside = $('#bootstrap_dialog .modal-body>:first-child');
    if (div_already_inside.lenght!=0) {
        $('#bootstrap_dialog .modal-body>:first-child').appendTo('body').hide();
    }
    $('#bootstrap_dialog .modal-body').append($('#'+id).show());
    $('#bootstrap_dialog').modal({
        show: true,
        });
}

function hm(id) {
    // Will be true if bootstrap is loaded, false otherwise
    var bootstrap_enabled = (typeof $().modal == 'function');
    if (bootstrap_enabled) {
        $('#bootstrap_dialog').modal('hide');
        $('#bootstrap_dialog .modal-body>:first-child').appendTo('body').hide();
    } else if ($('#'+id).is(':data(dialog)')) {
        $('#'+id).dialog("close");
    }
}

/* Top message */
var top_message_auto_close = true;
function show_top_message(type) {
	msg_class = (type=='E'?'error':(type=='W'?'warning':'info'));
    msg_delay = (msg_class=='info'?5000:10000);

    height = $('#top_message').height() + 32;
    $('#top_message').offset({top: -height});
    $('#top_message').show();

	$('#top_message').removeClass('error warning info').addClass(msg_class).animate({
              top : '-1px'
            }, 800);
	if (top_message_auto_close) setTimeout(hide_top_message, msg_delay);
}

function hide_top_message() {
    height = $('#top_message').height() + 32;
	$('#top_message').animate({
              top : -height
            }, 800);
}

function toggle_top_message() {
    top_message_auto_close = false;
	var is_visible = $('#top_message').offset().top - $(window).scrollTop() > -10;
	//console.log(is_visible);
	if (is_visible) {
		hide_top_message();
	}
	else {
		var type = ($('#top_message').hasClass('error')?'E':($('#top_message').hasClass('warning')?'W':'I'));
		show_top_message(type);
	}
}

function goToAnchor(element_id) {
    var elementPosition = $('#'+element_id).offset().top-100;
    $('html, body').animate({scrollTop:elementPosition}, 'slow');
    return false
}

function cw_login_dialog() {
    var login_dialog_div = $('#login_dialog');
    if (login_dialog_div.length==0) {
        login_dialog_div = $('<div id="login_dialog"></div>');
        $('body').append(login_dialog_div);
    } 

    var url = $(this).attr('href');
    if (!url) url = window.location.href;
    
    ajaxGet(current_location+'/index.php?target=acc_manager&mode=need_login&redirect_to='+encodeURIComponent(url),'');
    
    return false;
}

function append_url(url, params) {
    return (url.indexOf("?") != -1 ? url+'&'+params : url+'?'+params);
}

function cw_fire_event(element, eventname) {
    if ("createEvent" in document) {
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent(eventname, false, true);
        element.dispatchEvent(evt);
    } else
        element.fireEvent("on"+eventname);
}
