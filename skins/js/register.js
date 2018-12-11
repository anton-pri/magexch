function cw_register_init() {
    // Global var
    window.profile_form_obj = null;
    cw_register_validation_init();
}

function cw_address_select_init(user) {
    // Load first address
    ajaxGet('index.php?target=user&mode=addresses&action=load&address_id=main&user='+user+'&usertype='+reg_usertype);
}

function cw_address_init(country,state,name) {
    window.country = country;
    window.state = state;
    ajaxGet('index.php?target=ajax&mode=map&country='+country+'&state='+state+'&name='+name);
}

function cw_address_book_init() {
    $('ul.address_book li').bind('click',aAJAXClickHandler);
    $('ul.address_book li a').bind('click',aAJAXClickHandler);
}

function cw_register_validation_init() {
    window.profile_form_obj = $("#profile_form");
    window.profile_form_obj.validate({
        onkeyup: false //turn off auto validate whilst typing
    });
}
