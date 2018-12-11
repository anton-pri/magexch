function checkEmailAddress(field, empty_err) {
var err = false;
var res, x;
	if (!field)
		return true;

	if (field.value.length == 0) {
		if (empty_err != 'Y')
			return true;
		else
			err = true;
	}

	var arrEmail = field.value.replace(/^\s+/, '').replace(/\s+$/, '').split('@');
	if (arrEmail.length != 2 || arrEmail[0].length < 1)
		err = true;
	if (!err) {
		if (arrEmail[0].length > 2)
			res = arrEmail[0].search(/^[-\w][-\.\w]+[-\w]$/gi);
		else
			res = arrEmail[0].search(/^[-\w]+$/gi);
		if (res == -1)
			err = true;
	}
	if (!err) {
		var arr2Email = arrEmail[1].split('.');
		if (arr2Email.length < 2)
			err = true;
	}
	if (!err) {
		var domenTail = arr2Email[arr2Email.length-1];
		var _arr2Email = new Array();
		for (x = 0; x < arr2Email.length-1; x++)
			_arr2Email[x] = arr2Email[x];
		arr2Email = _arr2Email;
		var domen = arr2Email.join('.');
		res = domen.search(/^[-!#\$%&*+\\\/=?\.\w^`{|}~]+$/gi);
		if (res == -1)
			err = true;
		res = domenTail.search(/^[a-zA-Z]+$/gi);
		if (res == -1 || domenTail.length < 2 || domenTail.length > 6)
			err = true;
	}
//	/^([-\w][-\.\w]*)?[-\w]@([-!#\$%&*+\\\/=?\w^`{|}~]+\.)+[a-zA-Z]{2,6}$/gi

	if (err) {
		alert(txt_email_invalid);
		field.focus();
		field.select();
	}
	return !err;
}

