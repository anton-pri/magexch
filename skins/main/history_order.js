
function switch_details_mode(edit_mode, cur_btn, old_btn) {
	var dv = document.getElementById("details_view");
	var de = document.getElementById("details_edit");

	if (!dv || !de || edit_mode == details_mode)
		return;

	if (edit_mode) {
		dv.style.display = 'none';
		de.style.display = '';

	} else {
    	var rval = de.value;
	    for (var of in details_fields_labels) {
    	    var re = new RegExp(of, "g");
        	rval = rval.replace(re, details_fields_labels[of]);
	    }
    	dv.value = rval;

		dv.style.display = '';
		de.style.display = 'none';
	}

	details_mode = edit_mode;
	cur_btn.style.fontWeight = 'bold';
	old_btn.style.fontWeight = '';
}
