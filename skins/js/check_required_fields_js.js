function checkRequired(lFields, id) {
	if (!lFields || lFields.length == 0)
		return true;

	if (id) {
		for (var x = 0; x < lFields.length; x++) {
			if (lFields[x][0] == id) {
				lFields = [lFields[x]];
				break;
			}
		}
	}

	for (var x = 0; x < lFields.length; x++) {
		if (!lFields[x] || !document.getElementById(lFields[x][0]))
			continue;

		var obj = document.getElementById(lFields[x][0]);
		if ((obj.type == 'text' || obj.type == 'password' || obj.type == 'textarea') && !obj.value.search(/^[\s]*$/gi)) {
			if (lbl_required_field_is_empty != '') {
				alert(substitute(lbl_required_field_is_empty, 'field', lFields[x][1]));
			} else {
				alert(lFields[x][1]);
			}

			if (!obj.disabled && obj.type != 'hidden') {
				checkRequiredShow(obj);
				obj.focus();
			}

			return false;

		}
	}

	return true;
}

/*
	Show hidden element and element's parents
*/
function checkRequiredShow(elm) {
	if (elm.style && elm.style.display == 'none') {

		if (elm.id == 'ship_box' && document.getElementById('ship2diff')) {
			/* Exception for Register page */
			document.getElementById('ship2diff').checked = true;
			document.getElementById('ship2diff').onclick();
			
		} else
			elm.style.display = '';
	}

	if (elm.parentNode)
		checkRequiredShow(elm.parentNode);

}

