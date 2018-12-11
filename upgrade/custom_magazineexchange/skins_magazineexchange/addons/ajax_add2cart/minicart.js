var add2cart_popup_width = 376;
var add2cart_popup_height = 0;


$(document).ready(function(){
    if ($('#minicart').length) {
        $('#minicart').find('a.minicart_control').live('click',aAJAXClickHandler);
    }
});

function cw_submit_form_add2cart(form) {
    var form_obj =  $('form[name='+form+']');
    if (form_obj) {
        if (!form_obj.attr('id')) {
            form_obj.attr('id',form);
        }
        form_obj.attr('blockUI',form_obj.attr('id'));
    }

    // Create popup if it is not created yet
    // Server response will use it for reply
    var popup = $('#add2cart_popup');
    if (popup.length == 0) {
        popup = $('<div id="add2cart_popup"></div>');
        $('body').append(popup);
    }

    submitFormAjax.apply(form_obj,[form]);

}
