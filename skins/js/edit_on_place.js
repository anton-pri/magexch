$(document).ready(function(){
    $('body').on('focus', '.edit_on_place:not(span)', edit_on_place_add_tools);
    $('body').on('click','span.edit_on_place', edit_on_place_span);
    $('body').on('click','.edit_on_place_submit',edit_on_place_submit);
    $('body').on('click','.edit_on_place_cancel',edit_on_place_cancel);
});

function edit_on_place_span(e) {
    var span_offset = $(this).offset();
    if (span_offset.left + $(this).innerWidth() - e.pageX < 25) {
        var t = $(this).attr('token');
        var inp = $('<input type="text" value="'+$(this).text()+'" class="edit_on_place" token="'+t+'" for="span" />');
        $(this).hide();
        $(this).removeClass('edit_on_place');
        $(this).after(inp);
        inp.focus();
        return false
    } else {
        return true;
    }
}

function edit_on_place_add_tools() {
    edit_on_place_remove_tools();
    var inp = $(this);
    var t = inp.attr('token');
    var toolbox = $('<span class="edit_on_place_toolbox"></span>');
    var btn_ok = $('<button type="button" class="edit_on_place_submit" for="'+t+'" title="Save"><img src="'+images_dir+'/check_correct.png" alt="Save" /></button>');
    var btn_cancel = $('<button type="button" class="edit_on_place_cancel" for="'+t+'" title="Cancel"><img src="'+images_dir+'/check_wrong.png" alt="Cancel" /></button>');
    toolbox.append(btn_ok).append(btn_cancel);
    inp.after(toolbox);
    if (inp.prop('tagName').toUpperCase() == 'INPUT' 
        && inp.attr('type').toUpperCase() == 'CHECKBOX') {
        inp.data('old_value', inp.prop('checked'));
    } else {
        inp.data('old_value',inp.val());
    }
}

function edit_on_place_remove_tools(t) {
    $('.edit_on_place_toolbox').remove();
    if (t) {
         var inp = $('.edit_on_place[token='+t+']');
         if (inp.length && inp.attr('for')=='span') {
             
             var text = inp.val();
             inp.remove();
             var span = $('span[token='+t+']');
             if (span.length) {
                 if (span.children().length) {
                    span.children().eq(0).text(text);
                 } else {
                    span.text(text);
                 }
                 span.show();
                 span.addClass('edit_on_place');
             }
         }
    }
}

function edit_on_place_cancel() {
    var t = $(this).attr('for');
    var inp = $('.edit_on_place[token='+t+']');
    if (inp.prop('tagName').toUpperCase() == 'INPUT' 
        && inp.attr('type').toUpperCase() == 'CHECKBOX') {   
        inp.prop('checked',inp.data('old_value'));
    } else {             
        inp.val(inp.data('old_value'));
    }
    edit_on_place_remove_tools(t);
}

function edit_on_place_submit() {
    var t = $(this).attr('for');
    var inp = $('.edit_on_place[token='+t+']');
    
    var form  = $(document.createElement('form'));
    form.attr({
    'action' : 'index.php?target=edit_on_place',
    'method' : 'post'
    });
    var token = $(document.createElement('input'));
    token.attr({
    'type'  : 'hidden',
    'name'  : 'token',
    'value' : t
    });
    var copy = $(document.createElement('input'));
    copy.attr({
    'type'  : 'hidden',
    'name'  : 'value',
    'value' : inp.val()
    });

    if (inp.prop('tagName').toUpperCase() == 'INPUT' 
        && inp.attr('type').toUpperCase() == 'CHECKBOX' 
        && !inp.prop('checked')) {
            copy.val('');
    }

    form.append(copy);
    form.append(token);
    
    submitFormAjax(form, function(data){ if ($(data).find('top_message_type').text()!='E') edit_on_place_remove_tools(t)});
    return false;
}
