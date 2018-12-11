{include_once_src file="main/include_js.tpl" src="js/popup_files.js"}

{literal}
<script type="text/javascript">

$(document).ready(function() {

    var apply_btn = $("form[name='select_image_form'] .buttons .button-left");
    var tab_id = $('.section_tab_selected').attr('id');
    if(tab_id=='tab_on_local'){
      apply_btn.css('opacity', '1');
      apply_btn.attr('href', '#');

    }
    $('#tab_on_local, #tab_on_server, #tab_on_internet').click(function(){
       var tab_id = $(this).attr('id');
       var apply_btn = $("form[name='select_image_form'] .buttons .button-left");

        if(tab_id == 'tab_on_local'){
            apply_btn.css('opacity', '1');
            apply_btn.attr('href', '#');

        }else{
            apply_btn.css('opacity', '1');
            apply_btn.attr('href', 'javascript: cw_submit_form(document.select_image_form);');

        }
    });

    $('#fileupload').bind('fileuploadsubmit', function (e, data) {
        for (var i=0; i<data.files.length; i++){
            var max_size = '{/literal}{$upload_max_filesize}{literal}'.split('M')[0];
                max_size = max_size * 1024 * 1024;
            var img_size = data.files[i].size;
            var ext = data.files[i].type.split('/')[1];

                if((ext ===undefined)||(ext!='jpg')&&(ext!='jpeg')&&(ext!='png')&&(ext!='gif')){
                     $('#file_err').html('{/literal}{$lng.err_allowed_image_types}{literal}');
                    apply_btn.css('opacity', '0.4');
                    return false;

                }else if(img_size > max_size){
                    $('#file_err').html('{/literal}{$lng.err_file_bigger_than} {$upload_max_filesize}{literal}');
                    apply_btn.css('opacity', '0.4');
                    return false;
            }
        }
        data.formData = {
                        type:   $("form input[name='type']").val(),
                        imgid:  $("form input[name='imgid']").val(),
                        id:     $("form input[name='id']").val(),
                        source: $("form input[name='source']").val()
                        }
    });

/*
// Fileupload was disabled in CP-169.

    $('#fileupload').fileupload({
        url: $("form[name='select_image_form']").attr('action'),
        add: function (e, data) {
           apply_btn.css('opacity', '1');
           var file_name = data.files[0].name;
           $('.fileupload-name').html(file_name);
           data.context = apply_btn;
           apply_btn.unbind('click');
           data.context.click(function(e){
                e.preventDefault();
                data.submit();
            })

        },
        done: function (e, data) {
            document.open();
            document.write(data.result);
            document.close();
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css('width',  progress + '%');
        }
    })
*/
});

{/literal}
</script>
{capture name=section}
<div class="image_popup">
{jstabs}
default_tab={$js_tab|default:"search_orders"}
{if $current_area eq 'C'}
default_template=main/image_selection/image_selection_tabs.tpl
{else}
default_template=admin/image_selection/image_selection_tabs.tpl
{/if}
[submit]
title="{$lng.lbl_apply}"
href="javascript: cw_submit_form(document.select_image_form);"
style="btn btn-green"

{if !$tabs || in_array('on_local',$tabs)}
[on_local]
title="{$lng.lbl_file_on_local_computer} here"
{/if}

{if $current_area neq 'C' && (!$tabs || in_array('on_server',$tabs))}
[on_server]
title="{$lng.lbl_file_on_server}"
{/if}

{if !$tabs || in_array('on_internet',$tabs)}
[on_internet]
title="{$lng.lbl_file_on_internet}"
{/if}

{/jstabs}

<form class="block" action="index.php?target=image_selection" method="post" name="select_image_form" enctype="multipart/form-data">

<input type="hidden" name="type" value="{$type}" />
<input type="hidden" name="imgid" value="{$imgid}" />
<input type="hidden" name="id" value="{$id|default:$multiple_id}" />
<input type="hidden" name="source" value="" />
<input type="hidden" name="js_tab" id="form_js_tab" value="" />
{if $current_area eq 'C'}
{include file='tabs/js_tabs.tpl'}
{else}
{include file='admin/tabs/js_tabs.tpl'}
{/if}

</form>
</div>
{/capture}
{$smarty.capture.section}
{*include file='common/section.tpl' title=$lng.lbl_change_image content=$smarty.capture.section*}
