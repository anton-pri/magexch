$(document).ready(function(){

    var selected_metric = $("select[name='configuration[weight_unit_select]']").find(':selected').val();
    var selected_length = $("select[name='configuration[length_unit_select]']").find(':selected').val();

    var metric_opt_groups =  $("select[name='configuration[weight_unit_select]']").find("option[value='g_metric'], option[value='g_imperial'], option[value='g_custom']");
    var length_opt_groups =  $("select[name='configuration[length_unit_select]']").find("option[value='g_metric'], option[value='g_imperial'], option[value='g_custom']");

    var config_weight_inputs = $("input[name='configuration[weight_symbol]'], input[name='configuration[weight_symbol_grams]']");
    var config_length_inputs = $("input[name='configuration[dim_units]'], input[name='configuration[dimensions_symbol_cm]']");

    metric_opt_groups.prop('disabled', true);
    metric_opt_groups.css('font-weight', 'bold');
    metric_opt_groups.css('color', '#222222');
    length_opt_groups.prop('disabled', true);
    length_opt_groups.css('font-weight', 'bold');
    length_opt_groups.css('color', '#222222');


    if(selected_metric!='fill'){
        config_weight_inputs.prop('readonly', true);
        config_weight_inputs.css('opacity', 0.5);
    }
    if(selected_length != 'fill'){
        config_length_inputs.prop('readonly', true);
        config_length_inputs.css('opacity', 0.5);
    }

    $("select[name='configuration[weight_unit_select]']").change(function(){
        var selected_unit = $(this).val();
        var weight_symbol = $("input[name='configuration[weight_symbol]']");
        var weight_symbol_grams = $("input[name='configuration[weight_symbol_grams]']");

        if(selected_unit=='kg'){
            unit_input_change(weight_symbol, weight_symbol_grams, 'kg', '1000', true, config_weight_inputs);

        }else if(selected_unit=='g'){
            unit_input_change(weight_symbol, weight_symbol_grams, 'g', '1', true, config_weight_inputs);

        }else if(selected_unit=='mg'){
            unit_input_change(weight_symbol, weight_symbol_grams, 'mg', '0.001', true, config_weight_inputs);

        }else if(selected_unit=='lb'){
            unit_input_change(weight_symbol, weight_symbol_grams, 'lb', '453.59237', true, config_weight_inputs);

        }else if(selected_unit=='oz'){
            unit_input_change(weight_symbol, weight_symbol_grams, 'oz', '28.3495231', true, config_weight_inputs);

        }else if(selected_unit=='dr'){
            unit_input_change(weight_symbol, weight_symbol_grams, 'dr', '1.771845', true, config_weight_inputs);

        }else if(selected_unit=='gr'){
            unit_input_change(weight_symbol, weight_symbol_grams, 'gr', '0.064798', true, config_weight_inputs);

        }else if(selected_unit=='fill'){
            unit_input_change(weight_symbol, weight_symbol_grams, '', '', false, config_weight_inputs);

        }
    })

    $("select[name='configuration[length_unit_select]']").change(function(){
        var selected_unit = $(this).val();
        var dim_symbol = $("input[name='configuration[dim_units]']");
        var dim_symbol_cm = $("input[name='configuration[dimensions_symbol_cm]']");

        if(selected_unit=='meter'){
            unit_input_change(dim_symbol, dim_symbol_cm, 'm', '100', true, config_length_inputs);

        }else if(selected_unit=='cm'){
            unit_input_change(dim_symbol, dim_symbol_cm, 'cm', '1', true, config_length_inputs);

        }else if(selected_unit=='mm'){
            unit_input_change(dim_symbol, dim_symbol_cm, 'mm', '0.1', true, config_length_inputs);

        }else if(selected_unit=='yd'){
            unit_input_change(dim_symbol, dim_symbol_cm, 'yd', '91.44', true, config_length_inputs);

        }else if(selected_unit=='ft'){
            unit_input_change(dim_symbol, dim_symbol_cm, 'ft', '30.48', true, config_length_inputs);

        }else if(selected_unit=='in'){
            unit_input_change(dim_symbol, dim_symbol_cm, 'in', '2.54', true, config_length_inputs);

        }else if(selected_unit=='th'){
            unit_input_change(dim_symbol, dim_symbol_cm, 'th', '0.00254', true, config_length_inputs);

        }else if(selected_unit=='fill'){
            unit_input_change(dim_symbol, dim_symbol_cm, '', '', false, config_length_inputs);

        }
    })

})

function unit_input_change(input_unit_symbol, input_unit_size, unit_symbol,  unit_val, input_readonly, group_inputs){

    if(input_readonly){
        group_inputs.prop('readonly', true);
        group_inputs.css('opacity', 0.5);
    }else{
        group_inputs.prop('readonly', false);
        group_inputs.css('opacity', 1);
    }
    if(unit_symbol !='')
        input_unit_symbol.val(unit_symbol);
    if(unit_val !='')
        input_unit_size.val(unit_val);

}
