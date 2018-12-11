function moveSelect(left, right, type) {
	if (type != 'R') {
		var tmp = left;
		left = right;
		right = tmp;
	}
	if (!left || !right)
		return false;

	while (right.selectedIndex != -1) {
		left.options[left.options.length] = new Option(right.options[right.selectedIndex].text, right.options[right.selectedIndex].value);
		right.options[right.selectedIndex] = null;
	}

	return true;
}

function saveSelects(objects) {
	if (!objects)
		return false;

	if (document.zone_form.zone_name.value == '') {
		alert(msg_err_zone_rename);
		return false;
	}
	
	for (var sel = 0; sel < objects.length; sel++) {
		if (document.getElementById(objects[sel]))
			if (document.getElementById(objects[sel]+"_store").value == '')
				for (var x = 0; x < document.getElementById(objects[sel]).options.length; x++)
					document.getElementById(objects[sel]+"_store").value += document.getElementById(objects[sel]).options[x].value+";";
	}
	return true;
}

function cw_js_check_zone(zone, name) {
    var codes;

	var obj = document.getElementById(name);
	if (zone == 'ALL') {
		for (var x = obj.options.length-1; x >= 0; x--)
			$(obj.options[x]).attr('selected',true);

		return true;
	}

    codes = zones[zone];	
	if (codes) {
        for (var x = obj.options.length-1; x >= 0; x--)
			$(obj.options[x]).attr('selected',codes[obj.options[x].value] == 'Y');
    }
    else {
        for (var x = obj.options.length - 1; x >= 0; x--)
            $(obj.options[x]).attr('selected',false);
    }


}
