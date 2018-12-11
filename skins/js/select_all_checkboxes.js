$(document).ready(function() {
    // if exist checkbox with class select_all
    if (!$('input:checkbox.select_all').length) {
        return;
    }
    // bind click event
    $('body').on('click', 'input:checkbox.select_all', change_all_checkboxes);
});

function change_all_checkboxes() {
    // if exist class class_to_select
    if ($(this).attr('class_to_select') == undefined) {
        return;
    }
    var checked_state = $(this).prop('checked');        // check or uncheck all
    var class_to_select = $(this).attr('class_to_select');

    // if exist checkbox with class class_to_select
    if ($('input:checkbox.' + class_to_select) == undefined) {
        return;
    }

    // for each checkbox
    $('input:checkbox.' + class_to_select).each(function() {

        if (!$(this).prop('disabled')) {
            $(this).prop('checked', checked_state);
            // if exist onclick then run it
            if ($(this).attr("onClick") != undefined) {
                $(this)[0].onclick();
            }
        }
    });
}

function select_all_checkboxes(flag, formname, arr) {
    if (!formname) return;
    if (!arr) return;

    if (!document.forms[formname] || arr.length == 0)
        return false;

    for (var x = 0; x < arr.length; x++) {
        if (arr[x] != '' && document.forms[formname].elements[arr[x]] && !document.forms[formname].elements[arr[x]].disabled) {
            document.forms[formname].elements[arr[x]].checked = flag;
            if (document.forms[formname].elements[arr[x]].onclick)
                document.forms[formname].elements[arr[x]].onclick();
        }
    }
}
