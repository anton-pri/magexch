$(document).ready(function(){
    
    if($("input[name='fi_profile[col_names_line_id]']").val()==""){
        $('.adv_field_name').removeClass('adv_field_disabled');
    }

    $('#fi_profile .disabled .header_bordered :input').not('.adv_field_name').attr('disabled', 'disabled');

    if($("input[name='fi_profile[col_names_line_id]']").val()!=''){
        $('.adv_field_name').attr('disabled', 'disabled');
        $('.adv_field_name').addClass('adv_field_disabled');
    }
    $("input[name='fi_profile[col_names_line_id]']").change(function(){
        if($(this).val()!=''){
        $('.adv_field_name').attr('disabled', 'disabled');
            $('.adv_field_name').addClass('adv_field_disabled');

        }else{
            $('.adv_field_name').removeAttr('disabled');
            $('.adv_field_name').removeClass('adv_field_disabled');

        }
        })

    $("input[name='fi_profile[type]']:radio").change(function(){

        $('.fi_options').addClass('disabled');
        $('#fi_profile .disabled .header_bordered :input').not('.adv_field_name').attr('disabled', 'disabled');
        $(this).parents('.fi_options').removeClass('disabled');
        $(this).parents('.fi_options').find('input, select').not('.adv_field_name').removeAttr('disabled');
    });


        $('input[name=import_type]:radio').change(function(){
        var val = $(this).val()
        $('.import_from').hide();
        $('#import_from_'+val).fadeIn('slow');
    })

    $('#fi_test_file').change(function(){
        var files = document.getElementById('fi_test_file').files;
        var file = files[0];
        var num_lines = 10;
        var start = 0;
        var stop = 2400;
        var output = '';
        var reader = new FileReader();

        $('#pre_parsed_file').hide();
        $('#test_parsed_file').html('');
        $('#pre_parsed_file').show();
        reader.onload = function(e) {
            var text  = e.target.result;
            var lines = text.split(/[\r\n]+/g);
            for(var i = 0; i < lines.length; i++) {
                if (lines[i].trim() != ''){
                    output += lines[i] + "<div class='line_separator'></div>";
                }else{
                    num_lines++;
                }
                if(i==num_lines){ break;}
            }
             output = output.replace(/"/g, "\\'");
            $('#pre_parsed_file .input_field_0').html(output);
            $("#pre_parsed_file input[name='fi_profile[test_file_demo_content]']").attr('value', output);
        };

        var blob = file.slice(start, stop + 1);
        //reader.readAsBinaryString(blob);
        if (file.name.indexOf(".xlsx") > 1) {
            $('#pre_parsed_file .input_field_0').html("<h3>XSLX File selected <a href=\"javascript: cw_submit_form('process_profile', 'test_profile');\"> load and select sheet</a><div class='line_separator'></div></h3>");  
        } else
            reader.readAsArrayBuffer(blob);
    })


})


function dump(obj, dbgdiv) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }

    alert(out);

    // or, if you wanted to avoid alerts...

    //dbgdiv.innerHTML = out;
}

function add_column_fileds(num_fields){


    var current_num_fields = $('.fi_adv_column').length
    if(current_num_fields>num_fields){
        for(current_num_fields; num_fields< current_num_fields; current_num_fields--){
            $('#fi_adv_column_'+current_num_fields).remove();
        }
    }else{
    var field_html = '<tr id="fi_adv_column_1" class="fi_adv_column">'+$('#fi_adv_column_1').html()+'</div>';
    var data ='';
    for(i=current_num_fields+1; i<=num_fields; i++){
        data = field_html;
        data = data.replace(/[[1]]/g, i+']');
        data = data.replace(/_1/g, '_'+i);
        data = data.replace("<td>1", "<td>"+i);
        $('#fi_adv_columns_table').append(data);
    }

    }
}
