var multirowInputSets = {};

function add_inputset(name, obj, isLined, preset) {

	if (!name)
		return false;

	var buttonTD = obj;
    while (buttonTD.tagName.toUpperCase() != 'TD' && buttonTD.parentNode) {
        buttonTD = buttonTD.parentNode;
    }

	if (buttonTD.tagName.toUpperCase() != 'TD')
		return false;

	if (!buttonTD.inheritedRows) {
		buttonTD.inheritedRows = [];
	}

	buttonTD.isLined = isLined;

    if (isset(preset))
        return add_inputset_row(name, buttonTD, null, true);
    else
    	return add_inputset_row(name, buttonTD, buttonTD.parentNode, false);
}

/*
	Add new row
*/
function add_inputset_row(name, buttonTD, lastTR, isClear) {
	var regexp = new RegExp('^'+name+'_box', 'ig');

	/* Get last cloned row */
	var maxI = -1;
	if (buttonTD.inheritedRows.length > 0) {
		for (var i in buttonTD.inheritedRows) {
			maxI = i;
		}
	}

	if (!lastTR)
		lastTR = buttonTD.parentNode;//(maxI >= 0) ? buttonTD.inheritedRows[maxI] : buttonTD.parentNode;

	var origTable = lastTR.parentNode.parentNode;
	var origTR = lastTR;

	/* Clone row */
	maxI++;
	lastTR = origTable.insertRow(lastTR.rowIndex);

	/* Copy row attributes */
	for (var x = 0; x < origTR.attributes.length; x++) {
		if(!origTR.attributes[x].specified)
			continue;
		var newAttr = document.createAttribute(origTR.attributes[x].name);
		newAttr.value = origTR.attributes[x].value
		lastTR.attributes.setNamedItem(newAttr);
	}

	lastTR.buttonTD = buttonTD;
	lastTR.mark = name;
	lastTR.inheritedRowIndex = maxI;
	lastTR.cssText = origTR.cssText;

    var anyfocus = false;
	for (var x = 0; x < origTR.cells.length; x++) {
		if (origTR.cells[x].id.search(regexp) == -1)
			continue;

		/* Clone cell */
		var curTD = lastTR.appendChild(origTR.cells[x].cloneNode(true));
        ind = origTR.cells[x].id.match(/([\d+])/);
		curTD.id = name+'_box_'+ind[1]+'_'+lastTR.inheritedRowIndex;

		/* Change clone element name (in clone cell) */
		for (var y = 0; y < curTD.childNodes.length; y++) {
			var elm = curTD.childNodes[y];
			if (!elm.tagName)
				continue;

			var tName = elm.tagName.toUpperCase();
			if (tName == 'INPUT' || tName == 'SELECT' || tName == 'TEXTAREA' || tName == 'IMG') {
                if (elm.name.search(/\[[0-9]+\]\[[^0-9]+\]$/) != -1) {
                    elm.name = elm.name.replace(/\[[0-9]+\]\[(.*)\]$/, '['+(lastTR.inheritedRowIndex+1)+'][$1]');
                }
				else if (elm.name.search(/\[[0-9]+\]\[\]$/) != -1) {
					elm.name = elm.name.replace(/\[[0-9]+\]\[\]$/, '['+(lastTR.inheritedRowIndex+1)+'][]');
				} else {
					elm.name = elm.name.replace(/\[[0-9]+\]$/, '['+(lastTR.inheritedRowIndex+1)+']');
				}
                if (elm.id)
                    elm.id = elm.id.replace(/(.*)_[0-9]+$/, '$1_'+(lastTR.inheritedRowIndex+1));

// kornev, we should set the unique value for the radio element
                if (tName == 'INPUT' && elm.type == 'radio') elm.value = maxI+1;

				/* Clear cloned element content if noCloneContent option is enabled for this multirow inputset */
				if ((multirowInputSets[name] && multirowInputSets[name].noCloneContent) || isClear) {
					if ((tName == 'INPUT' && (elm.type == 'text' || elm.type == 'hidden')) || tName == 'TEXTAREA')
						elm.value = '';
					else if (tName == 'INPUT' && elm.type == 'checkbox') elm.checked = false;
				}
                if (!anyfocus) {
                    elm.focus();
                    anyfocus = true;
                }
			}
		}
	}


    var originalTR = $(origTR);
    var clonedTR = $(lastTR);

    // Special trick to copy selected values. jquery.clone() looses selection by default.
    if (!isClear) {

        //get original selects into a jq object
        var originalSelects = originalTR.find('select');

        clonedTR.find('select').each(function(index, item) {
             //set new select to value of old select
             $(item).val( originalSelects.eq(index).val() );
        });

    }

    // Clear original pattern row
    originalTR.find(':input').each(function() {
        var type = this.type;
        var tag = this.tagName.toLowerCase(); // normalize case
        // it's ok to reset the value attr of text inputs,
        // password inputs, and textareas
        if (type == 'text' || type == 'password' || tag == 'textarea')
          this.value = "";
        // checkboxes and radios need to have their checked state cleared
        // but should *not* have their 'value' changed
        else if (type == 'checkbox' || type == 'radio')
          this.checked = false;
        // select elements need to have their 'selectedIndex' property set to 0 (-1 for absolute clear)
        // (this works for both single and multiple select elements)
        else if (tag == 'select')
          this.selectedIndex = 0;
    });

    buttonTD.inheritedRows[maxI] = lastTR;

	/* Add service cell (with + / - buttons) */
	curTD = lastTR.insertCell(-1);
	curTD.noWrap = true;
	if (!window.lbl_remove_row)
		lbl_remove_row = "Remove row";
	if (!window.lbl_add_row)
		lbl_add_row = "Add row";

	if (window.inputset_minus_img)
		curTD.innerHTML = '<a href="javascript: void(0);" onclick="javascript: remove_inputset(this);"><img src="'+inputset_minus_img+'" alt="'+lbl_remove_row+'" /></a>';
	else
		curTD.innerHTML = '<a href="javascript: void(0);" onclick="javascript: remove_inputset(this);">'+lbl_remove_row+'</a>';

	lined_inputset(buttonTD);

	$('body').trigger('event_add_inputset_row', [name, maxI+1]);

	return lastTR;
}

/*
	Add new row (onclick event handler)
*/
function add_inputset_subrow(tr) {
    while (tr.tagName.toUpperCase() != 'TR' && tr.parentNode)
        tr = tr.parentNode;

	if (tr.tagName.toUpperCase() != 'TR')
		return false;

//kornev, because we have to clean the new row
	add_inputset_row(tr.mark, tr.buttonTD, tr, true);
}

/*
	Remove row from rows set
*/
function remove_inputset(tr) {
	while (tr.tagName.toUpperCase() != 'TR' && tr.parentNode)
		tr = tr.parentNode;

	if (tr.tagName.toUpperCase() != 'TR')
		return false;

	if (!tr.buttonTD.inheritedRows[tr.inheritedRowIndex])
		return false;

	tr.parentNode.parentNode.deleteRow(tr.rowIndex);
	lined_inputset(tr.buttonTD);
	tr.buttonTD.inheritedRows[tr.inheritedRowIndex] = null;
	delete tr;

	return true;
}

/*
	Display rows set as lined
*/
function lined_inputset(buttonTD) {
	if (!buttonTD.isLined || buttonTD.inheritedRows.length == 0)
		return false;

	var origTable = buttonTD;
	while (origTable.tagName.toUpperCase() != 'TABLE' && origTable.parentNode)
		origTable = origTable.parentNode;

	if (origTable.tagName.toUpperCase() != 'TABLE')
		return false;

	var maxRowIndex = buttonTD.parentNode.rowIndex;
	for (var i in buttonTD.inheritedRows) {
		if (buttonTD.inheritedRows[i] && maxRowIndex < buttonTD.inheritedRows[i].rowIndex)
			maxRowIndex = buttonTD.inheritedRows[i].rowIndex;
	}

	var flag = true;
	for (var i = 0; i < origTable.rows.length; i++) {
		if (origTable.rows[i].rowIndex > buttonTD.parentNode.rowIndex && origTable.rows[i].rowIndex <= maxRowIndex) {
			origTable.rows[i].className = flag ? 'TableSubHead' : '';
			flag = !flag;
		}
	}
}

/*
	Add row with preset data
*/
function add_inputset_preset(name, obj, isLined, preset) {
	var tr = add_inputset(name, obj, isLined, true);
	if (!tr)
		return false;

	for (p in preset) {
		var elm = false;
		for (var i = 0; i < tr.cells.length && !elm; i++) {

			/* Get element */
			var elm = add_inputset_search_element(tr.cells[i], preset[p].regExp);
            if (!elm)
				continue;

			var tName = elm.tagName.toUpperCase();
			if (tName == 'INPUT' && (elm.type == 'text' || elm.type == 'hidden')) {
				elm.value = preset[p].value;

			} else if (tName == 'INPUT' && elm.type == 'checkbox') {
				elm.checked = preset[p].value == '0' ? false : true;
			} else if (tName == 'INPUT' && elm.type == 'radio') {
				var elms = elm.form.elements[elm.name];
				if (elms) {
					for (var y = 0; y < elms.length; y++) {
						elms[y].checked =  (elms[y].value == preset[p].value);
                    }
				}

			} else if (tName == 'SELECT') {
				for (var y = 0; y < elm.options.length; y++)
					if (elm.options[y].value == preset[p].value) {
						elm.options[y].selected = true;
						break;
					}

			} else if (tName == 'TEXTAREA') {
				elm.value = preset[p].value;
			}
		
		}
	}
}

/*
	Search element by name (RegExp) recursive
*/
function add_inputset_search_element(parent, regExp) {
	if (parent.childNodes.length == 0)
		return false;

	for (var i = 0; i < parent.childNodes.length; i++) {
        el = parent.childNodes[i];

		if (!el.tagName || !el.name)
			continue;

		var tName = el.tagName.toUpperCase();

		if ((tName == 'INPUT' || tName == 'SELECT' || tName == 'TEXTAREA') && (el.name.search(regExp) != -1 || (el.id && el.id.search(regExp) != -1))) {
			return el;
		}

		if (el.parentChilds && el.parentChilds.length > 0) {
			var r = add_inputset_search_element(el, regExp);
			if (r)
				return r;
		}
	}

	return false;
}

function show_hide_description_preset(el, name) {
	var description_ident_name = "default_values_select";
	if (name == 'dmsl') {
		description_ident_name = "default_values_multiselect";
	}

	if (attribute_description == undefined) {
		var attribute_description = 'Description';
	}

	var temp_id = $(el).next().attr('id');
	var key = temp_id.split('_').pop();
	if (key == 0) {
		var dsl_id = name + '_box_0';
	} else {
		var _key = key - 1;
		var dsl_id = name + '_box_0_' + _key;
	}

	var description_value = $('#' + description_ident_name + '_description_' + key).val();
	var textarea_id = description_ident_name + '_textarea_' + key;

	if ($('#' + textarea_id).length) {
		$(el).html('&#9658;');
		$('#' + textarea_id).slideUp(300);
		setTimeout(function() {
			$('#' + textarea_id).parent().parent().remove();
		}, 300);
	} else {
		$(el).html('&#9660;');
		$('#' + dsl_id).parent().after('<tr><td colspan="100%"><textarea class="form-control" style="display: none" id="' + textarea_id + '" placeholder="' + attribute_description + '">' + description_value + '</textarea></td></tr>');
		$('#' + textarea_id).slideDown(300);

		$('#' + textarea_id).on('keyup', function(e) {
			var temp_id = $(this).attr('id');
			var _key = temp_id.split('_').pop();
			$('#' + description_ident_name + '_description_' + _key).val($(this).val());
		});
	}
}
