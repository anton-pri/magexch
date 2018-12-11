
var old_onclick;
var is_open = false;

function rememberXY() {
	if (!window.opener)
		return;

	if (localBFamily == 'MSIE') {
/*
		window.opener.defaultLabelWindowX = window.screenLeft;
		window.opener.defaultLabelWindowY = window.screenTop;
*/
	} else {
		window.opener.defaultLabelWindowX = window.screenX;
		window.opener.defaultLabelWindowY = window.screenY;
	} 
}

function restoreLabel() {
	if (!window.opener)
		return;

	document.lf.val.value = window.opener.initialValue;
	if (window.opener.setLabel)
		window.opener.setLabel();
}

function getData() {
	if (!window.opener) {
		window.close();
		return;
	}

	/* get label id */
	for (var i = 0; document.lf.elements.length; i++) {
		if (document.lf.elements[i].name == 'name') {
			document.lf.elements[i].value = window.opener.labelID;
			break;
		}
	}

	/* display label id */
	document.getElementById('labelName').innerHTML = window.opener.labelID;

	/* put label to text area */
	document.lf.val.value = window.opener.xunescape(window.opener.lng_labels[window.opener.labelID]);

	resizeWnd();

	if (document.lf.val.focus)
		document.lf.val.focus();
	if (document.lf.val.select)
		document.lf.val.select();

	if (document.getElementById('valEnb')) {
		old_onclick = document.getElementById('valEnb').onclick;
		document.getElementById('valEnb').onclick = function(e) {
			if (old_onclick)
				old_onclick();
			is_open = true;
			resizeWnd();
		}
	}
	window.focus();

}

function resizeWnd() {
	var w = document.getElementById("tbl").offsetWidth;
	var h = document.getElementById("tbl").offsetHeight;
	window.innerWidth = w+23;
	window.innerHeight = h+20;
	window.resizeTo(w+33, h+60);
}

function copyText(submit) {
	if (window.valEditor && is_open)
		document.getElementById('valAdv').value = document.lf.val.value = valEditor.getXHTMLBody();
	if (window.opener)
		window.opener.setLabel();
}

