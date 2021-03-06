/* Append hidden submit button to each form. It allows to submit form by pressing Enter according to HTML5 spec. */

$(document).ready(function(){
    $('form').each(function(){
        if ($(this).find('input[type=submit]').length == 0) {
            var sbm = $('<input type="submit" />');
            sbm.css('display','none');
            sbm.attr('data','autogenerated');
            $(this).append(sbm);
        }

    });
    $('input:checkbox').each(function(){
        if ($(this).is(":checked")){
            $(this).wrap( "<div class='custom_checkbox checked'></div>" );
        } else {
            $(this).wrap( "<div class='custom_checkbox'></div>" );
        }
    });

    $('input:checkbox').change(function(){
        //console.log('changed');
        $(this).closest('.custom_checkbox').toggleClass('checked');
    });
    var mainCheckbox = $('input:checkbox.select_all').closest('.custom_checkbox');
    mainCheckbox.on('click', null, change_all_checkboxes_admin);
/*
    $('input').iCheck({
        checkboxClass: 'icheckbox_minimal',
        radioClass: 'iradio_minimal',
    });

    $('#active_sections input').on('ifChecked', function(event){
      var id = $(this).attr('id')+'_section';
      $('#'+id).toggle();
    })
    $('#active_sections').find('.icheckbox_minimal').click(function(){
      var id = $(this).find('input').attr('id')+'_section';
      console.log('id');
      $('#'+id).toggle();
    });
*/
});

function change_all_checkboxes_admin () {
    // if exist class class_to_select
    var checkB = $(this).find('input:checkbox');
    if (checkB.attr('class_to_select') == undefined) {
        return;
    }
    var checked_state = checkB.prop('checked');        // check or uncheck all
    var class_to_select = checkB.attr('class_to_select');
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
            if (!checked_state){
                $(this).closest('.custom_checkbox').removeClass('checked');
            } else {    
                $(this).closest('.custom_checkbox').addClass('checked');
            }
        }
    });
}
