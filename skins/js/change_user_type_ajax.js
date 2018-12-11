// TODO: These functions are not used anymore, changing of usertype on fly is prohibited. Remove this file and ajax PHP handler 
function handler_memberships(data) {
    select = document.getElementById(data.el_membership);

    if (!select) return;

    while (select.options.length > 0)
        select.options[select.options.length-1] = null;

    select.options[select.options.length] = new Option(lbl_please_select, 0);
    if (data.membership_count > 0) {
        for (i in data.memberships)
            select.options[select.options.length] = new Option(data.memberships[i].membership, i);
    }
}

function cw_user_ajax_change_memberships(usertype, el_membership) {

    if (!el_membership) return;

    $.ajax({ 
    "url":"index.php?target=ajax&mode=memberships&usertype="+usertype+"&el_membership="+el_membership,
    "success":handler_memberships, 
    "dataType":"json", 
    "type":"post"
    });
}
