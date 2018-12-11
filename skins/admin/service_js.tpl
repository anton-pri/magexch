{load_defer file="jquery/jquery-1.11.3.min.js" type="js"}
{load_defer file="jquery/jquery-ui.min.js" type="js"}
{load_defer file="jquery/validation/jquery.validate.min.js" type="js"}
{load_defer file="jquery/validation/additional-methods.min.js" type="js"}
{load_defer file="js/jquery.ezmark.js" type="js"}
{load_defer file="jquery/jquery.blockUI.js" type="js"}
{load_defer file="jquery/jquery.blockUI.defaults.js" type="js"}
{load_defer file="js/ajax.js" type="js"}
{load_defer file="jquery/form/jquery.form.js" type="js"}
{load_defer file="js/select_all_checkboxes.js" type="js"}
{load_defer file="jquery/jquery.lingsTooltip.min.js" type="js"}
{load_defer file="jquery/tagsinput/jquery.tagsinput.js" type="js"}
{load_defer file="jquery/tagsinput/jquery.slimscroll.min.js" type="js"}
{load_defer file="jquery/icheck.min.js" type="js"}
{load_defer file="js/render_tooltip.js" type="js"}
{load_defer file="js/sticky.js" type="js"}
{load_defer file="jquery/file_upload/jquery.ui.widget.js" type="js"}
{load_defer file="jquery/file_upload/jquery.fileupload.js" type="js"}
{load_defer file="jquery/colorpicker/spectrum.js" type="js"}
{load_defer file="js/edit_on_place.js" type="js"}
{load_defer file="js/bootstrap_ui.js" type="js"}
{load_defer file="js/admin.js" type="js"}

{if $include.js}
{foreach from=$include.js item=file}
{load_defer file="`$file`" type="js"}
{/foreach}
{/if}

{capture name=service_js_init}
{literal}
	$(document).ready(function() {
		jQuery.extend(jQuery.validator.messages, {
			required: lbl_field_is_required 
		});

	});
	

{/literal}
{/capture}
{load_defer file="service_js_init" direct_info=$smarty.capture.service_js_init type="js" queue=100}
{load_defer_code type="js"}

<script type="text/javascript" src='{$SkinDir}/ckeditor/ckeditor.js'></script>

