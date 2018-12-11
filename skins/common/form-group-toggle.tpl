{assign var='curr_target_mode' value=`$current_target`/`$mode`}
{if in_array($curr_target_mode, array('products/details', 'products/add', 'categories/edit'))}
{tunnel func='cw_admin_forms_display_get' via='cw_call' param1=$current_target param2=$mode assign='hidden_fg_inputs'}
<script type="text/javascript">
var hidden_fg_inputs = [];
var hidden_form_groups = [];
{foreach from=$hidden_fg_inputs item=el}hidden_fg_inputs.push('{$el}');{/foreach}
var lbl_hide = '{$lng.lbl_hide}';
var lbl_show = '{$lng.lbl_show}';
var lbl_1_field_hidden = '{$lng.lbl_1_field_hidden}';
var fg_target = '{$current_target}';
var fg_mode = '{$mode}';
var fg_show_all = false;
{literal}
var lbl_x_fields_hidden = '{{x}} Fields Hidden';
function fa_eye_show_all_btn(hide) {

    if (hidden_fg_inputs.length > 0) { 
        $('#fa-eye-show-all').show();
        if (hide) 
            $('#fa-eye-show-all').effect('shake', {}, 900).effect('pulsate', {}, 900);

        if (hidden_fg_inputs.length == 1)
            $('#fa-eye-show-all-button').attr('title', lbl_1_field_hidden);
        else 
            $('#fa-eye-show-all-button').attr('title', lbl_x_fields_hidden.replace('{{x}}', hidden_fg_inputs.length));

    } else {
        $('#fa-eye-show-all').hide(); 
    }
}

function fa_toggle_element(fg_id, fg_idx, hide) {
    if (hide) {
        hidden_fg_inputs.push(fg_idx);        
        hidden_form_groups.push(fg_id);
        if (!fg_show_all) { 
            $('#'+fg_id).slideUp();  
        }
        $('#'+fg_id).find('.hide_eye').attr('title', lbl_show);
        $('#'+fg_id).find('.hide_eye').removeClass('hide_eye').addClass('display_eye');
         
    } else {
        hidden_fg_inputs = $.grep(hidden_fg_inputs, function(value) {
            return value != fg_idx;
        }); 
        hidden_form_groups = $.grep(hidden_form_groups, function(value) {
            return value != fg_id;
        });
        $('#'+fg_id).find('.display_eye').attr('title', lbl_hide);
        $('#'+fg_id).find('.display_eye').removeClass('display_eye').addClass('hide_eye');
    }
}

$(document).ready(function() {
    var tc = 0;

    fa_eye_show_all_btn(false);

    $('#fa-eye-show-all').on('click', function() {
        if (!fg_show_all) {
            $.each(hidden_form_groups, function( key, value ) {
                $('#' + value).slideDown();
            }); 
            fg_show_all = true;
        } else {
            $.each(hidden_form_groups, function( key, value ) {
                $('#' + value).slideUp();
            }); 
            fg_show_all = false;
        }
    });

    $('.form-horizontal > .form-group').not('.required').each(function() {
        var fg_idx = ''; 

        $(this).find('input:text, input:password, input:file, select, textarea, input:button').each(function() {
            if ($(this).attr('name') != undefined && $(this).attr('name') != '') { 
                fg_idx += $(this).attr('name');   
            }
        });

        if (fg_idx != '') {
            fg_idx = fg_idx.replace(/\[|\]/g,'');
            var fg_action = 'hide';
            var fg_icon_title = lbl_hide;

            var fg_id = $(this).attr('id');
            if (fg_id == undefined) {
                fg_id = 'fg_'+ tc;
                $(this).attr('id', fg_id);
            }

            if (hidden_fg_inputs.indexOf(fg_idx) > -1) {
                $(this).hide();
                hidden_form_groups.push(fg_id);
                fg_action = 'display';
                fg_icon_title = lbl_show;
            } 
  
            var fg_js_code = "javascript: ajaxGet('index.php?target=forms_display&fg_target="+fg_target+"&fg_mode="+fg_mode+"&fg_idx="+fg_idx+"&fg_id="+fg_id+"')";
            var extra_icon = "<div class=\""+fg_action+"_eye\" title=\""+fg_icon_title+"\"><a href=\""+fg_js_code+"\"><i class=\"fa fa-eye-slash\"></i></a></div>";   
            $(this).append(extra_icon);
 
            tc++;
        }
    });
});
{/literal}
</script>
{/if}
